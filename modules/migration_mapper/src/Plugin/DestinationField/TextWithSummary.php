<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'text with summary' destination field type.
 *
 * @DestinationField(
 *   id = "text_with_summary",
 *   name = @Translation("Text With Summary"),
 *   fieldTypes = {
 *     "text_with_summary",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class TextWithSummary extends DestinationFieldBase {

}
