<?php

namespace Drupal\migration_mapper\Plugin\DestinationType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\DestinationType;
use Drupal\migration_mapper\DestinationTypeBase;
use Drupal\node\Entity\NodeType;

/**
 * Provides a 'node' entity type.
 *
 * @DestinationType(
 *   id = "node",
 *   name = @Translation("Node"),
 *   excludedFields = {"type"},
 * )
 */
class Node extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getProcessYml($bundle) {
    return [
      'type' => [
        'plugin' => 'default_value',
        'default_value' => $bundle,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity:node',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $node_types = NodeType::loadMultiple();
    $options = [];
    foreach ($node_types as $node_type) {
      $options[$node_type->id()] = $node_type->label();
    }
    return $options;
  }

}
