<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for destination type plugins.
 */
interface DestinationTypeInterface extends PluginInspectionInterface {

  /**
   * Return the name of the destination type.
   *
   * @return string
   *   Name string.
   */
  public function getName();

  /**
   * An array of fields to be excluded.
   *
   * @return array
   *   Array of excluded field ids.
   */
  public function getExcludedFields();

  /**
   * Add form elements specific to a destination type.
   *
   * @param array $form
   *   Form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Form array.
   */
  public function getEntityFormExtras(array $form, FormStateInterface $form_state);

  /**
   * Get list of this entity type.
   *
   * @return array
   *   List of bundle types.
   */
  public function getDestinationTypes();

  /**
   * Get process section to be encoded to yml.
   *
   * @param string $bundle
   *   Bundle name.
   *
   * @return array
   *   Export array.
   */
  public function getProcessYml($bundle);

  /**
   * Get destination section to be encoded to yml.
   *
   * @return array
   *   Export array.
   */
  public function getDestinationYml();

}
