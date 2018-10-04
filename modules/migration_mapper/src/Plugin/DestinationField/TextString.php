<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'string' destination field type.
 *
 * @DestinationField(
 *   id = "string",
 *   name = @Translation("String"),
 *   fieldTypes = {
 *     "string",
 *     "email",
 *     "path",
 *     "telephone",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class TextString extends DestinationFieldBase {

}
