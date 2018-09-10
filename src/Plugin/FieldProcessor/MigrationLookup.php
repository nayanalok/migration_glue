<?php

namespace Drupal\migration_glue\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides 'migration_lookup' process plugin.
 *
 * @FieldProcessor(
 *   id = "migration_lookup",
 *   name = @Translation("Migration Lookup"),
 * )
 */
class MigrationLookup extends FieldProcessorBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['migration'] = [
      '#type' => 'select',
      '#description' => $this->t('Please select migration.'),
      '#title' => $this->t('Migration'),
      '#options' => $this->getMigrationList(),
    ];

    $form['no_stub'] = [
      '#type' => 'select',
      '#description' => $this->t('If we want to generate the stub of child/dependent migration entity.'),
      '#title' => $this->t('No stub'),
      '#options' => [
        1 => $this->t('Yes'),
        0 => $this->t('No'),
      ],
    ];

    $form['use_extract'] = [
      '#type' => 'select',
      '#title' => $this->t('Use extract'),
      '#description' => $this->t('If we have an array result and we want to extract value from the specific index key.'),
      '#options' => [
        1 => $this->t('Yes'),
        0 => $this->t('No'),
      ],
    ];

    $form['index'] = [
      '#type' => 'number',
      '#description' => $this->t('Please provide the index position like 0,1 etc. The normal use case for this is in the paragraph reference
      migration where we get target_id and target_revision_id in index 0 and 1 respectively and thus we can define index for them.'),
      '#default_value' => 0,
      '#title' => $this->t('Index'),
      '#states' => [
        'visible' => [
          ['select[name$="[' . 'use_extract' . ']"]' => ['value' => 1]],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    $use_extract = $config['use_extract'];
    $extract = [];
    if ($use_extract) {
      $extract = [
        'plugin' => 'extract',
        'index' => [
          $config['index'],
        ],
      ];
    }
    return [
      [
        'plugin'      =>  'migration_lookup',
        'migration'   =>  $config['migration'] ?: '',
        'no_stub'     =>  $config['no_stub'],
        'source'      =>  $field_data['map_to'],
      ],
      $extract,
    ];
  }

  /**
   * Get list of migration.
   *
   * @return array
   */
  protected function getMigrationList() {
    // @TODO: Load migration instead of hardcoded.
    return [
      'example_id' => 'example_id',
    ];
  }

}
