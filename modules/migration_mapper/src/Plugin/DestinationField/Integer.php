<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'integer' destination field type.
 *
 * @DestinationField(
 *   id = "integer",
 *   name = @Translation("Integer"),
 *   fieldTypes = {
 *     "integer",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class Integer extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (int) $value;
  }

}
