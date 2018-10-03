<?php

namespace Drupal\migration_mapper\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\FieldProcessor;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides an 'explode' field processor.
 *
 * @FieldProcessor(
 *   id = "explode",
 *   name = @Translation("Explode"),
 * )
 */
class Explode extends FieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['explode_delimiter'] = [
      '#type' => 'textfield',
      '#title' => 'Delimiter',
      '#size' => 5,
    ];

    $form['explode_limit'] = [
      '#type' => 'textfield',
      '#title' => 'Limit',
      '#default_value' => '100',
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
      'plugin' => 'explode',
      'source' => $field_data['map_to'],
      'limit' => (int) $config['explode_limit'],
      'delimiter' => $config['explode_delimiter'],
    ];
  }

}
