<?php

namespace Drupal\acquia_platform_migration\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides 'inline_images' process plugin.
 *
 * @FieldProcessor(
 *   id = "inline_images",
 *   name = @Translation("Inline Images"),
 * )
 */
class InlineImages extends FieldProcessorBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    // Todo: Need a way for the Class:Object type callable.
    return [
      'plugin'      =>  'inline_images',
      'source'      =>  $field_data['map_to'],
    ];
  }

}
