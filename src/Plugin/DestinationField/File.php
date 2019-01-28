<?php

namespace Drupal\acquia_platform_migration\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'image' destination field type.
 *
 * @DestinationField(
 *   id = "file",
 *   name = @Translation("File"),
 *   fieldTypes = {
 *     "file"
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class File extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [
      'plugin' => 'file_import',
      'source' => $field_data['map_to'],
      'destination' => 'public://files/',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (int) $value;
  }

}
