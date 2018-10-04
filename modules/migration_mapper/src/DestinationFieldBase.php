<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base destination field object.
 */
class DestinationFieldBase extends PluginBase implements DestinationFieldInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  public function supportedFieldTypes() {
    return $this->pluginDefinition['fieldTypes'];
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [
      $field_name => trim($field_data['map_to']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function combineWithPlugin() {
    return $this->pluginDefinition['combinePlugin'];
  }

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return $value;
  }

}
