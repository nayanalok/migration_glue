<?php

namespace Drupal\migration_mapper\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\FieldProcessor;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides a 'concat' field processor.
 *
 * @FieldProcessor(
 *   id = "concat",
 *   name = @Translation("Concat"),
 * )
 */
class Concat extends FieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['concat'] = [
      '#markup' => 'Not functional yet.',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [
      'plugin' => 'concat',
      'source' => ['foo' , 'bar'],
      'delimiter' => '/',
    ];
  }

}
