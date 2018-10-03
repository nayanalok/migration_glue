<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for data source plugins.
 */
interface DataSourceInterface extends PluginInspectionInterface {

  /**
   * Return the name of the data source.
   *
   * @return string
   *   Name string.
   */
  public function getName();

  /**
   * Process inputted csv data.
   *
   * @param string $data
   *   Data inputted from the user.
   * @param array $config
   *   Data source related config.
   *
   * @return array
   *   List of columns found.
   */
  public function process(string $data, array $config);

  /**
   * Add form elements specific to a data source.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Source section form elements for data source.
   */
  public function getSourceConfigForm(FormStateInterface $form_state);

  /**
   * Add form elements specific to a data source & destination type.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Form elements needed for destination type.
   */
  public function getDestinationConfigForm(FormStateInterface $form_state);

  /**
   * Get source section.
   *
   * @param FormStateInterface $form_state
   *   Data inputted from the user.
   *
   * @return array
   *   Export array to be encoded to yml.
   */
  public function getSourceSection(FormStateInterface $form_state);

}
