<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'boolean' destination field type.
 *
 * @DestinationField(
 *   id = "boolean",
 *   name = @Translation("Boolean"),
 *   fieldTypes = {
 *     "boolean",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class Boolean extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (bool) $value;
  }

}
