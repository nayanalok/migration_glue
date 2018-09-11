<?php

namespace Drupal\migration_glue\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'file_uri' destination field type.
 *
 * @DestinationField(
 *   id = "file_uri",
 *   name = @Translation("File URI"),
 *   fieldTypes = {
 *     "file_uri"
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class File extends DestinationFieldBase {

}
