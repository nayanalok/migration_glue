<?php

namespace Drupal\migration_mapper\Form;

use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\migrate_plus\Entity\MigrationGroupInterface;
use Drupal\migration_mapper\DataSourceInterface;
use Drupal\migration_mapper\DestinationFieldInterface;
use Drupal\migration_mapper\DestinationTypeInterface;
use Drupal\migration_mapper\FieldProcessorInterface;
use Drupal\migration_mapper\Plugin\DataSourceManager;
use Drupal\migration_mapper\Plugin\DestinationFieldManager;
use Drupal\migration_mapper\Plugin\DestinationTypeManager;
use Drupal\migration_mapper\Plugin\EntityTypeManager;
use Drupal\migration_mapper\Plugin\FieldProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration admin form.
 */
class MigrationAdminForm extends FormBase {

  /**
   * The data source manager.
   *
   * @var DataSourceManager
   */
  protected $dataSourceManager;

  /**
   * The destination type manager.
   *
   * @var DestinationTypeManager
   */
  protected $destinationTypeManager;

  /**
   * The destination field manager.
   *
   * @var DestinationFieldManager
   */
  protected $destinationFieldManager;

  /**
   * The field processor manager.
   *
   * @var FieldProcessorManager
   */
  protected $fieldProcessorManager;

  /**
   * Entity field manager.
   *
   * @var EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new MigrationAdminForm object.
   *
   * @param \Drupal\migration_mapper\Plugin\DataSourceManager $data_source_manager
   *   The data source manager.
   * @param \Drupal\migration_mapper\Plugin\DestinationTypeManager $destination_type_manager
   *   The destination type manager.
   * @param \Drupal\migration_mapper\Plugin\DestinationFieldManager $destination_field_manager
   *   The destination field manager.
   * @param \Drupal\migration_mapper\Plugin\FieldProcessorManager $field_processor_manager
   *   The field processor manager.
   * @param \Drupal\Core\Entity\EntityFieldManager $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    DataSourceManager $data_source_manager,
    DestinationTypeManager $destination_type_manager,
    DestinationFieldManager $destination_field_manager,
    FieldProcessorManager $field_processor_manager,
    EntityFieldManager $entity_field_manager,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->dataSourceManager = $data_source_manager;
    $this->destinationTypeManager = $destination_type_manager;
    $this->destinationFieldManager = $destination_field_manager;
    $this->fieldProcessorManager = $field_processor_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.data_source'),
      $container->get('plugin.manager.destination_type'),
      $container->get('plugin.manager.destination_field'),
      $container->get('plugin.manager.field_processor'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'migration_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['migration_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Migration ID'),
      '#default_value' => $form_state->getValue('migration_id', 'example_id'),
    ];
    $form['migration_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Migration Label'),
      '#default_value' => $form_state->getValue('migration_label', 'Example Label'),
    ];

    $migration_groups = $this->entityTypeManager
      ->getStorage('migration_group')
      ->loadMultiple();
    $options = [];
    /** @var MigrationGroupInterface $migration_group */
    foreach ($migration_groups as $migration_group) {
      // Set the item in the correct language for display.
      $options[$migration_group->id()] = $migration_group->label();
    }

    $form['migration_group'] = [
      '#type' => 'radios',
      '#default_value' => $form_state->getValue('migration_group', '_none'),
      '#options' => array_merge(['_none' => 'None'], $options),
      '#title' => $this->t('Migration Group'),
    ];

    // Source section.
    $form['source'] = [
      '#type' => 'details',
      '#title' => $this->t('Source'),
      '#open' => TRUE,
    ];

    // Retrieves all data source plugins.
    $data_source_plugins = $this->getPluginOptions($this->dataSourceManager);
    // Defaults to first plugin if nothing has been selected.
    $default_data_source = $form_state->getValue('data_source', current(array_keys($data_source_plugins)));
    // Data source plugin options.
    $form['source']['data_source'] = [
      '#type' => 'radios',
      '#title' => $this->t('Data Source Types'),
      '#default_value' => $default_data_source,
      '#options' => $data_source_plugins,
      '#ajax' => [
        'callback' => [$this, 'updateDataSourceConfig'],
        'wrapper' => 'data-source-config-wrapper',
      ],
    ];
    // Add any extra form inputs related to selected data source.
    $form['source']['data_source_config_wrapper'] = [
      '#type' => 'container',
      '#prefix' => '<div id="data-source-config-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];
    // Get data source plugin to see if extra inputs are needed.
    $data_source_plugin = $this->dataSourceManager->createInstance($default_data_source);
    $temp = $data_source_plugin->getSourceConfigForm($form_state);
    $form['source']['data_source_config_wrapper'] = array_merge($form['source']['data_source_config_wrapper'], $temp);

    // Retrieves all destination type plugins.
    $destination_type_plugins = $this->getPluginOptions($this->destinationTypeManager);
    // Defaults to first plugin if nothing has been selected.
    $default_destination = $form_state->getValue('destination_type', current(array_keys($destination_type_plugins)));
    // Select list of destination types.
    $form['source']['destination_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Destination Types'),
      '#default_value' => $default_destination,
      '#options' => $destination_type_plugins,
    ];
    // Input for pasting data.
    $form['source']['data_source_data'] = [
      '#title' => $this->t('Paste your source data here:'),
      '#type' => 'textarea',
      '#default_value' => $form_state->getValue('data_source_data'),
    ];
    // Trigger import of data.
    $form['source']['import'] = [
      '#type' => 'button',
      '#value' => 'Import',
      '#name' => 'import',
      '#ajax' => [
        'callback' => [$this, 'importFromSource'],
        'wrapper' => 'process-wrapper',
      ],
    ];
    // Triggering element used for detecting ajax calls.
    $triggering_element = $form_state->getTriggeringElement();
    // Set flag to know whether data has been imported.
    if (!$imported = $form_state->has('imported')) {
      if ($triggering_element['#name'] == 'import') {
        $form_state->set('imported', TRUE);
        $imported = TRUE;
      }
    }

    // Process section.
    $form['process'] = [
      '#type' => 'details',
      '#title' => $this->t('Process'),
      '#prefix' => '<div id="process-wrapper">',
      '#suffix' => '</div>',
    ];
    if (!$imported) {
      $form['process']['no_data'] = [
        '#markup' => $this->t('No data has been imported yet.'),
      ];
    }
    // Loads selected destination type plugin.
    $destination_type_plugin = $this->destinationTypeManager->createInstance($default_destination);
    // The following relies on imported data.
    if ($imported) {
      // @todo better validation for importing
      if (!empty($form_state->getValue('data_source_data'))) {
        $data_source_id = $form_state->getValue('data_source');
        $inputted_data = $form_state->getValue('data_source_data');
        $data_source_config = $form_state->getValue('data_source_config_wrapper');
        $data_source_plugin = $this->dataSourceManager->createInstance($data_source_id);
        // Process inputted data and finds headers.
        $columns = $data_source_plugin->process($inputted_data, $data_source_config);
        $form_state->set('columns', $columns);
        // @todo do something better than this markup.
        $form['process']['columns'] = [
          '#markup' => implode(', ', array_keys($columns)),
        ];
        $form['process']['data_destination_config_wrapper']['data_destination_config'] = $data_source_plugin->getDestinationConfigForm($form_state);
      }
      // Retrieves the selected entities bundle types.
      $destination_bundle_types = $destination_type_plugin->getDestinationTypes();
      $default_bundle = $form_state->getValue('destination_bundle_list', '_none');
      $form['process']['destination_bundle_list'] = [
        '#type' => 'select',
        '#title' => $destination_type_plugin->getName() . ' ' . $this->t('Types'),
        '#default_value' => $default_bundle,
        '#empty_option' => $this->t('Please select a node type.'),
        '#empty_value' => '_none',
        '#options' => $destination_bundle_types,
        '#ajax' => [
          'callback' => [$this, 'updateFieldMapping'],
          'wrapper' => 'field-mapping-wrapper',
        ],
        '#access' => $imported,
      ];
      $form['process']['field_mapping'] = [
        '#type' => 'container',
        '#prefix' => '<div id="field-mapping-wrapper">',
        '#suffix' => '</div>',
      ];

      // Show field mapping section if a bundle has been selected.
      if ($default_bundle != '_none') {
        $form['process']['field_mapping']['field_group'] = [
          '#tree' => TRUE,
        ];
        $plugin_id = $destination_type_plugin->getPluginId();
        $fields = $this->entityFieldManager->getFieldDefinitions($plugin_id, $default_bundle);
        uasort($fields, function ($left, $right) {
          return $right->isRequired() - $left->isRequired();
        });
        /** @var BaseFieldDefinition $columns */
        $columns = $form_state->get('columns');
        $form_values = $form_state->getValues();

        // Separate custom and base fields.
        $form['process']['field_mapping']['field_group']['custom'] = [
          '#type' => 'details',
          '#title' => 'Custom Fields',
        ];
        $form['process']['field_mapping']['field_group']['base'] = [
          '#type' => 'details',
          '#title' => 'Base fields',
        ];
        foreach ($fields as $key => $field) {
          $field_type = $field->getType();
          $field_name = $field->getName();
          // Don't show excluded destination type fields.
          if (in_array($field_name, $destination_type_plugin->getExcludedFields())) {
            unset($fields[$key]);
            continue;
          }
          $field_group = ($field->getFieldStorageDefinition()->isBaseField()) ? 'base' : 'custom';
          $field_label = $field_name . (($field->isRequired()) ? '*' : '');
          // Get destination field plugins that match the field type.
          $destination_fields = $this->destinationFieldManager->getOptions($field_type);
          $field_input = [
            'title' => [
              '#type' => 'item',
              '#title' => $field_label . ' - ' . $field_type,
            ],
          ];
          // Show msg in the rare case a field type is not matched.
          if (empty($destination_fields)) {
            $field_input['map_to'] = [
              '#type' => 'item',
              '#description' => $field_type,
              '#prefix' => '<div>',
              '#suffix' => '</div>',
            ];
          }
          else {
            // Default set if there is an exact match with a plugin name.
            $default_type = '_none';
            if (isset($destination_fields[$field_type])) {
              $default_type = $field_type;
            }
            elseif (count($destination_fields) == 1) {
              $default_type = current(array_keys($destination_fields));
            }
            // Map the column to the field or exclude.
            $field_input['map_to'] = [
              '#type' => 'select',
              '#default_value' => $field_name,
              '#empty_option' => 'Do not include',
              '#empty_value' => '_none',
              '#options' => array_merge(['_default' => 'Set Default'], $columns),
            ];
            // Wrapper for processors.
            $field_input['field_processor_wrapper'] = [
              '#type' => 'container',
              '#attributes' => ['id' => $field_name . '-processor-wrapper'],
            ];
            $processor_added = FALSE;
            // Triggers could be add processor or processor type change.
            $trigger_field_match = strpos($triggering_element['#name'], $field_name) !== FALSE;
            $remove_processor = ($trigger_field_match && $triggering_element['#value'] == 'Remove');
            $field_values = $form_values['field_group'][$field_group][$field_name];
            if (!$remove_processor &&
              ($trigger_field_match || isset($field_values['field_processor_wrapper']['processor_selector']))) {
              $processor_added = TRUE;
              // Retrieves all field processor plugins.
              $field_processor_plugins = $this->getPluginOptions($this->fieldProcessorManager);
              $default_processor = $field_values['field_processor_wrapper']['processor_selector'];
              $field_input['field_processor_wrapper']['processor_selector'] = [
                '#type' => 'select',
                '#title' => 'Processor',
                '#empty_option' => 'Select',
                '#empty_value' => '_none',
                '#default_value' => (empty($default_processor)) ? $default_processor : '_none',
                '#options' => $field_processor_plugins,
                '#ajax' => [
                  'callback' => [$this, 'getProcessorConfig'],
                  'wrapper' => $field_name . '-added-processor-wrapper',
                ],
                '#states' => [
                  'invisible' => [
                    'select[name="field_group[' . $field_name . '][map_to]"]' => ['value' => '_none'],
                  ],
                ],
              ];
              $field_input['field_processor_wrapper']['added_processor_wrapper'] = [
                '#type' => 'container',
                '#prefix' => '<div id="' . $field_name . '-added-processor-wrapper">',
                '#suffix' => '</div>',
              ];
              if (!empty($default_processor) && $default_processor != '_none') {
                /** @var FieldProcessorInterface $field_processor_plugin */
                $field_processor_plugin = $this->fieldProcessorManager->createInstance($default_processor);
                $field_input['field_processor_wrapper']['added_processor_wrapper']['config'] = $field_processor_plugin->getFieldProcessorConfig();
              }
              $field_input['field_processor_wrapper']['remove_processor'] = [
                '#type' => 'button',
                '#value' => 'Remove',
                '#name' => $field_name . '_remove_processor',
                '#ajax' => [
                  'callback' => [$this, 'removeProcessor'],
                  'wrapper' => $field_name . '-processor-wrapper',
                ],
                '#states' => [
                  'invisible' => [
                    'select[name="[' . $field_name . '][map_to]"]' => ['value' => '_none'],
                  ],
                ],
                '#attributes' => [
                  'class' => ['processor_btn'],
                ],
              ];
            }
            if (!$processor_added) {
              // Adds processor to this fields import.
              $field_input['field_processor_wrapper']['add_processor'] = [
                '#type' => 'button',
                '#value' => 'Add processor',
                '#name' => $field_name . '_add_processor',
                '#ajax' => [
                  'callback' => [$this, 'addProcessor'],
                  'wrapper' => $field_name . '-processor-wrapper',
                ],
                '#states' => [
                  'invisible' => [
                    ['select[name$="[' . $field_name . '][map_to]"]' => ['value' => '_none']],
                    'or',
                    ['select[name$="[' . $field_name . '][map_to]"]' => ['value' => '_default']],
                  ],
                ],
                '#attributes' => [
                  'class' => ['processor_btn'],
                ],
              ];
              $default_value = $field_values['field_processor_wrapper']['default_value'];
              $field_input['field_processor_wrapper']['default_value'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Default Value'),
                '#default_value' => $default_value,
                '#states' => [
                  'visible' => [
                    ['select[name$="[' . $field_name . '][map_to]"]' => ['value' => '_default']],
                  ],
                ],
              ];
            }

            // Destination field type selection.
            $field_input['destination'] = [
              '#title' => 'Field Format',
              '#type' => 'select',
              '#default_value' => $default_type,
              '#options' => $destination_fields,
              '#states' => [
                'invisible' => [
                  'select[name="field_group[' . $field_name . '][map_to]"]' => ['value' => '_none'],
                ],
              ],
              '#access' => count($destination_fields) > 1,
            ];
          }
          // Add the above to the form.
          $field_container = [
            '#type' => 'container',
            '#prefix' => '<div id="' . $field_name . '-wrapper">',
            '#suffix' => '</div>',
          ];
          $form['process']['field_mapping']['field_group'][$field_group][$field_name] = array_merge($field_container, $field_input);
        }
      }
      // Exports everything to yml.
      $form['process']['export'] = [
        '#type' => 'button',
        '#value' => 'Export',
        '#name' => 'export',
        '#ajax' => [
          'callback' => [$this, 'updateExport'],
          'wrapper' => 'output-wrapper',
        ],
        '#states' => [
          'invisible' => [
            'select[name="destination_bundle_list"]' => ['value' => '_none'],
          ],
        ],
      ];
    }
    // Section for showing formatted migrate yml.
    $form['output'] = [
      '#type' => 'details',
      '#title' => $this->t('Output'),
      '#prefix' => '<div id="output-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['output']['export'] = [
      '#title' => $this->t('Here is your configuration:'),
      '#type' => 'textarea',
      '#rows' => 24,
    ];

    $form['#attached']['library'][] = 'migration_mapper/base';

    return $form;

  }

  /**
   * Adds a processor for processing data.
   */
  public function getProcessorConfig($form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -1));
    return $element['added_processor_wrapper'];
  }

  /**
   * Adds a processor for processing data.
   */
  public function addProcessor($form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();
    // Go one level up in the form, to the widgets container.
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -1));
    return $element;
  }

  /**
   * Removes a processor.
   */
  public function removeProcessor($form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();
    // Go one level up in the form, to the widgets container.
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -1));
    return $element;
  }

  /**
   * Handles switching the export textarea.
   */
  public function updateExport($form, FormStateInterface $form_state) {
    // Migration main configuration.
    $main_config = [];
    $main_config['id'] = $form_state->getValue('migration_id', 'example_id');
    $main_config['label'] = $form_state->getValue('migration_label', 'Example Label');
    if ($migration_group = $form_state->getValue('migration_group')) {
      if ($migration_group != '_none') {
        $main_config['migration_group'] = $migration_group;
      }
    }
    // Source section.
    $data_source = $form_state->getValue('data_source');
    /** @var DataSourceInterface $instance */
    $data_source_plugin = $this->dataSourceManager->createInstance($data_source);
    $source_data = $data_source_plugin->getSourceSection($form_state);

    // Process section.
    $field_groups = $form_state->getValue('field_group');

    $destination_type = $form_state->getValue('destination_type');
    $destination_bundle = $form_state->getValue('destination_bundle_list');
    /** @var DestinationTypeInterface $destination_type_plugin */
    $destination_type_plugin = $this->destinationTypeManager->createInstance($destination_type);
    $destination_process_yml = $destination_type_plugin->getProcessYml($destination_bundle);
    $process_data = ['process' => $destination_process_yml];
    foreach ($field_groups as $field_group) {
      foreach ($field_group as $field_name => $field_data) {
        if (isset($field_data['destination'])) {
          if ($field_data['map_to'] == '_default') {
            /** @var DestinationFieldInterface $destination_field */
            $destination_field = $this->destinationFieldManager->createInstance($field_data['destination']);
            $default_value = $destination_field->formatDefault($field_data['field_processor_wrapper']['default_value']);
            $field_yml = [
              $field_name => [
                'plugin' => 'default_value',
                'default_value' => $default_value,
              ],
            ];
          }
          elseif ($field_data['map_to'] != '_none') {
            $field_yml = [$field_name => []];
            $processor_yml = FALSE;
            if (isset($field_data['field_processor_wrapper']['processor_selector']) &&
              $field_data['field_processor_wrapper']['processor_selector'] != '_none'
            ) {
              /** @var FieldProcessorInterface $field_processor */
              $field_processor = $this->fieldProcessorManager->createInstance($field_data['field_processor_wrapper']['processor_selector']);
              $processor_yml = $field_processor->getExport($field_name, $field_data, $form_state);
            }
            /** @var DestinationFieldInterface $destination_field */
            $destination_field = $this->destinationFieldManager->createInstance($field_data['destination']);
            $destination_yml = $destination_field->getExport($field_name, $field_data, $form_state);
            if ($processor_yml) {
              if ($destination_field->combineWithPlugin()) {
                $field_yml[$field_name][] = $processor_yml;
                $field_yml[$field_name][] = $destination_yml;
              }
              else {
                $field_yml[$field_name] = $processor_yml;
              }
            }
            else {
              $field_yml = (count($destination_yml) == 1) ? $destination_yml : [$field_name => $destination_yml];
            }
          }
          if (!empty($field_yml)) {
            $process_data['process'] = array_merge($process_data['process'], $field_yml);
          }
        }

      }
    }
    // @todo handle the destination type export stuff.
    $destination_data = ['destination' => $destination_type_plugin->getDestinationYml()];
    $dependencies = ['dependencies' => ['enforced' => ['module' => ['migration_mapper']]]];
    $output_yml = Yaml::encode(array_merge($main_config, $source_data, $process_data, $destination_data, $dependencies));
    $form['output']['export']['#value'] = $output_yml;
    return $form['output'];
  }

  /**
   * {@inheritdoc}
   */
  public function updateDataSourceConfig(array &$form, FormStateInterface $form_state) {
    return $form['source']['data_source_config_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function updateFieldMapping(array &$form, FormStateInterface $form_state) {
    return $form['process']['field_mapping'];
  }

  /**
   * {@inheritdoc}
   */
  public function importFromSource(array &$form, FormStateInterface $form_state) {
    return $form['process'];
  }

  /**
   * Returns an array of plugins from the set manager for use as select options.
   *
   * @param string $plugin_manager
   *   Plugin manager instance.
   *
   * @return array
   *   Array of plugins.
   */
  public function getPluginOptions($plugin_manager) {
    $plugins = $plugin_manager->getDefinitions();
    $options = [];
    foreach ($plugins as $plugin_id => $plugin) {
      $instance = $plugin_manager->createInstance($plugin_id);
      $options[$plugin_id] = $instance->getName();
    }
    return $options;
  }

  /**
   * Returns an array of plugin instances from the set manager.
   *
   * @param string $plugin_manager
   *   Plugin manager instance.
   *
   * @return array
   *   Array of plugin instances.
   */
  public function getPluginInstances($plugin_manager) {
    $plugins = $plugin_manager->getOptions();
    $instances = [];
    foreach ($plugins as $plugin_id => $plugin) {
      $instance = $plugin_manager->createInstance($plugin_id);
      $instances[$plugin_id] = $instance;
    }
    return $instances;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
