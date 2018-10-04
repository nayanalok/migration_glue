<?php

namespace Drupal\migration_mapper\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a field processor annotation object.
 *
 * Plugin Namespace: Plugin\migration_mapper\DestinationField.
 *
 * @see \Drupal\migration_mapper\Plugin\DestinationFieldManager
 * @see plugin_api
 *
 * @Annotation
 */
class FieldProcessor extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the destination field.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

}
