<?php

namespace Drupal\migration_mapper;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base field processor object.
 */
class FieldProcessorBase extends PluginBase implements FieldProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [];
  }

}
