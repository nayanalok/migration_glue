<?php

namespace Drupal\migration_mapper\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a destination field annotation object.
 *
 * Plugin Namespace: Plugin\migration_mapper\DestinationField.
 *
 * @see \Drupal\migration_mapper\Plugin\DestinationFieldManager
 * @see plugin_api
 *
 * @Annotation
 */
class DestinationField extends Plugin {

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

  /**
   * An array of field types the plugin supports.
   *
   * @var array
   */
  public $fieldTypes = [];

  /**
   * Combine with any process plugins.
   *
   * @var bool
   */
  public $combinePlugin;

}
