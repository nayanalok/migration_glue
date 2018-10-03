<?php

namespace Drupal\migration_mapper\Plugin\DestinationType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\DestinationType;
use Drupal\migration_mapper\DestinationTypeBase;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Provides a 'taxonomy' entity type.
 *
 * @DestinationType(
 *   id = "taxonomy_term",
 *   name = @Translation("Taxonomy"),
 *   excludedFields = {"vid"},
 * )
 */
class Taxonomy extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getEntityFormExtras(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['some_extra'] = [
      '#type' => 'textfield',
      '#title' => 'Some Extra',
      '#size' => 10,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessYml($bundle) {
    return [
      'vid' => [
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
      'plugin' => 'entity:taxonomy_term',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $vocabularies = Vocabulary::loadMultiple();
    $options = [];
    foreach ($vocabularies as $vocabulary) {
      $options[$vocabulary->id()] = $vocabulary->label();
    }
    return $options;
  }

}
