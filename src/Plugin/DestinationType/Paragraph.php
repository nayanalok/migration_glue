<?php

namespace Drupal\migration_glue\Plugin\DestinationType;

use Drupal\migration_mapper\DestinationTypeBase;
use Drupal\paragraphs\Entity\ParagraphsType;

/**
 * Provides a 'Paragraph' destination entity type.
 *
 * @DestinationType(
 *   id = "paragraph",
 *   name = @Translation("Paragraph"),
 *   excludedFields = {"type"},
 * )
 */
class Paragraph extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity_reference_revisions:paragraph',
    ];
  }

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
  public function getDestinationTypes() {
    $para_types = ParagraphsType::loadMultiple();
    $options = [];
    foreach ($para_types as $para_type) {
      $options[$para_type->id()] = $para_type->label();
    }
    return $options;
  }

}
