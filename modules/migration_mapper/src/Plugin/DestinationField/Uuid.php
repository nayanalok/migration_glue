<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'uuid' destination field type.
 *
 * @DestinationField(
 *   id = "uuid",
 *   name = @Translation("UUID"),
 *   fieldTypes = {"uuid"},
 *   combinePlugin = FALSE,
 * )
 */
class Uuid extends DestinationFieldBase {

}
