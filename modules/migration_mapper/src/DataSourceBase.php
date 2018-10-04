<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base data source object.
 */
class DataSourceBase extends PluginBase implements DataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  public function process(string $data, array $config) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceConfigForm(FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationConfigForm(FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceSection(FormStateInterface $form_state) {
    return [];
  }

}
