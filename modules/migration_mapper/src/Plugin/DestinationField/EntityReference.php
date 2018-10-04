<?php

namespace Drupal\migration_mapper\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\migration_mapper\Annotation\DestinationField;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'entity reference' destination field type.
 *
 * @DestinationField(
 *   id = "entity_reference",
 *   name = @Translation("Entity Reference"),
 *   fieldTypes = {
 *     "entity_reference"
 *   },
 *   combinePlugin = TRUE,
 * )
 */
class EntityReference extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    return [
      'plugin' => 'entity_generate',
      'source' => $field_data['map_to'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formatDefault(string $value) {
    return (int) $value;
  }

}
