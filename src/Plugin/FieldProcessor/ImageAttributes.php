<?php

namespace Drupal\acquia_platform_migration\Plugin\FieldProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migration_mapper\FieldProcessorBase;

/**
 * Provides a 'image_attributes' field processor.
 *
 * @FieldProcessor(
 *   id = "image_attributes",
 *   name = @Translation("Image attributes"),
 * )
 */
class ImageAttributes extends FieldProcessorBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFieldProcessorConfig() {
    $form = [];
    $form['title'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Please provide xpath of title like /pages/page/title'),
      '#title' => $this->t('Title field'),
    ];

    $form['alt'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Please provide xpath of alt like /pages/page/alt'),
      '#title' => $this->t('Alt field'),
    ];

    $form['height'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Please provide xpath of height like /pages/page/height'),
      '#title' => $this->t('Height field'),
    ];

    $form['width'] = [
      '#type' => 'textfield',
      '#description' => $this->t('Please provide xpath of width like /pages/page/width'),
      '#title' => $this->t('Width field'),
    ];

    $form['destination'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Destination'),
      '#required' => TRUE,
      '#default_value' => 'public://images/',
      '#description' => $this->t('This should be like public://images/'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $config = $field_data['field_processor_wrapper']['added_processor_wrapper']['config'];
    return [
      'plugin'      =>  'image_import',
      'source'      =>  $field_data['map_to'],
      'destination' =>  $config['destination'] ?: '',
      'title'       =>  $config['title'] ?: '',
      'alt'         =>  $config['alt'] ?: '',
      'height'      =>  $config['height'] ?: '',
      'width'       =>  $config['width'] ?: '',
    ];
  }

}
