<?php

namespace Drupal\acquia_platform_migration\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides 'callback' process plugin.
 *
 * @FieldProcessor(
 *   id = "callback",
 *   name = @Translation("Callback"),
 * )
 */
class Callback extends FieldProcessorBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['callback'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Please provide a php callback.'),
      '#title' => $this->t('Callback'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    // Todo: Need a way for the Class:Object type callable.
    return [
      'plugin'      =>  'callback',
      'callable'    =>  $config['callback'] ?: '',
      'source'      =>  $field_data['map_to'],
    ];
  }

}
