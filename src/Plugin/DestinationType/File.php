<?php

namespace Drupal\migration_glue\Plugin\DestinationType;

use Drupal\migration_mapper\DestinationTypeBase;

/**
 * Provides a 'file' destination entity type.
 *
 * @DestinationType(
 *   id = "file",
 *   name = @Translation("File"),
 *   excludedFields = {"type"},
 * )
 */
class File extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity:file',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $options = ['file' => 'File'];
    return $options;
  }

}
