<?php

namespace Drupal\acquia_platform_migration\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'image' destination field type.
 *
 * @DestinationField(
 *   id = "image",
 *   name = @Translation("Image"),
 *   fieldTypes = {
 *     "image"
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class Image extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (int) $value;
  }

}
