<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'timestamp' destination field type.
 *
 * @DestinationField(
 *   id = "timestamp",
 *   name = @Translation("Timestamp"),
 *   fieldTypes = {
 *   "created",
 *   "changed",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class Timestamp extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (int) $value;
  }

}
