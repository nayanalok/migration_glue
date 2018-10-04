<?php

namespace Drupal\migration_mapper\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\FieldProcessor;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides an 'default_value' field processor.
 *
 * @FieldProcessor(
 *   id = "default_value",
 *   name = @Translation("Default Value"),
 * )
 */
class DefaultValue extends FieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => 'Value',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    return [
      'plugin' => 'default_value',
      'default_value' => $config['value'],
    ];
  }

}
