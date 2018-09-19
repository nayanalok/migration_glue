<?php

namespace Drupal\migration_glue\Plugin\DestinationType;

use Drupal\migration_mapper\DestinationTypeBase;
use Drupal\comment\Entity\CommentType;

/**
 * Provides a 'comment' destination entity type.
 *
 * @DestinationType(
 *   id = "comment",
 *   name = @Translation("Comment"),
 *   excludedFields = {"comment_type", "entity_type"},
 * )
 */
class Comment extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity:comment',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessYml($bundle) {
    // Entity to which this comment type is attached.
    $entity_type = CommentType::load($bundle)->getTargetEntityTypeId();
    return [
      'comment_type' => [
        'plugin' => 'default_value',
        'default_value' => $bundle,
      ],
      'entity_type' => [
        'plugin' => 'default_value',
        'default_value' => $entity_type,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $comment_types = CommentType::loadMultiple();
    $options = [];
    foreach ($comment_types as $comment_type) {
      $options[$comment_type->id()] = $comment_type->label();
    }
    return $options;
  }

}
