<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'language' destination field type.
 *
 * @DestinationField(
 *   id = "language",
 *   name = @Translation("Language"),
 *   fieldTypes = {"language"},
 *   combinePlugin = FALSE,
 * )
 */
class Language extends DestinationFieldBase {

}
