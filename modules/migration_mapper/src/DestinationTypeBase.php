<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base destination type object.
 */
class DestinationTypeBase extends PluginBase implements DestinationTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  public function getExcludedFields() {
    return $this->pluginDefinition['excludedFields'];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFormExtras(array $form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessYml($bundle) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    return [];
  }

}
