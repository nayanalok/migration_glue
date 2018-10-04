<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for field processor plugins.
 */
interface FieldProcessorInterface extends PluginInspectionInterface {

  /**
   * Return the name of the destination field processor.
   *
   * @return string
   *   Name string.
   */
  public function getName();

  /**
   * Return form config specific to this processor.
   *
   * @return array
   *   Form elements for field processor.
   */
  public function getFieldProcessorConfig();

  /**
   * Return export yml specific to this processor.
   *
   * @param string $field_name
   *   Field name.
   * @param array $field_data
   *   Field data.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Export array to be encoded in yml for field processor.
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state);

}
