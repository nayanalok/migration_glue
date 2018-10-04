<?php

namespace Drupal\migration_mapper\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\FieldProcessor;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides an 'substr' field processor.
 *
 * @FieldProcessor(
 *   id = "substr",
 *   name = @Translation("Substr"),
 * )
 */
class Substr extends FieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['start'] = [
      '#type' => 'textfield',
      '#title' => 'Start',
      '#size' => 5,
    ];

    $form['length'] = [
      '#type' => 'textfield',
      '#title' => 'Length',
      '#size' => 5,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    return [
      'plugin' => 'substr',
      'source' => $field_data['map_to'],
      'start' => (int) $config['start'],
      'length' => (int) $config['length'],
    ];
  }

}
