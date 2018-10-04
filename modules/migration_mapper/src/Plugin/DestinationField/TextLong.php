<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'text long' destination field type.
 *
 * @DestinationField(
 *   id = "text_long",
 *   name = @Translation("Text Long"),
 *   fieldTypes = {
 *     "string_long",
 *     "text_long",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class TextLong extends DestinationFieldBase {

}
