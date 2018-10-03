<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for destination field plugins.
 */
interface DestinationFieldInterface extends PluginInspectionInterface {

  /**
   * Return the name of the destination field.
   *
   * @return string
   *   Name string.
   */
  public function getName();

  /**
   * An array of field types this plugin supports.
   *
   * @return array
   *   Array of supported field types.
   */
  public function supportedFieldTypes();

  /**
   * Returns an field export array for yml conversion.
   *
   * @param string $field_name
   *   Field id.
   * @param array $field_data
   *   Field data.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Export array to convert to yml.
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state);

  /**
   * Return true if needs to be added to yml with plugin otherwise false.
   *
   * @return bool
   *   True or false.
   */
  public function combineWithPlugin();

  /**
   * Casts value to be of correct type.
   *
   * @param string $value
   *   Field value.
   *
   * @return mixed
   *   Mixed return value depending on type.
   */
  public function formatDefault(string $value);

}
