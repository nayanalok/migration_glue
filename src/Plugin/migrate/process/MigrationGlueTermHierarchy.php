<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Converts a given string into term hierarchy by separator provided.
 *
 * Example:-
 *  If a string/source be provided like 'Programming/Language/Html/Css'
 *  then with the separator '/', term tree will be created like
 *  Programing -> Language -> Html -> Css and tid of the leaf term 'Css'
 *  will be returned.
 *
 * Available configuration keys:
 * - separator: The value by which source to be split.
 * - bundle: Vocabulary name in which term needs to create.
 *
 * If no field plugin for the given field type is found, NULL will be returned.
 *
 * Example:
 *
 * @code
 * process:
 *   type:
 *     plugin: acquia_platform_migration_term_hierarchy
 *     separator: '/'
 *     bundle: 'tags'
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "acquia_platform_migration_term_hierarchy"
 * )
 */
class AcquiaPlatformMigrationTermHierarchy extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, EntityTypeManagerInterface $entityTypeManager, Connection $database) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityTypeManager = $entityTypeManager;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    if (empty($this->configuration['separator'])) {
      throw new MigrateException('You need to specify the seperator config on the plugin.');
    }

    if (empty($this->configuration['bundle'])) {
      throw new MigrateException('You need to specify the bundle config on the plugin.');
    }

    $parentId = 0;
    foreach (explode($this->configuration['separator'], $value) as $tname) {
      // If term not exists with a given parent.
      if (empty($tid = $this->findTerm($tname, $parentId))) {
        // Create the term.
        $term = [
          'vid' => $this->configuration['bundle'],
          'name' => $tname,
          'parent' => $parentId,
        ];
        $tid = $this->createTerm($term);
      }

      $parentId = $tid;
    }

    return $parentId;

  }

  /**
   * Find the term in a given vocab with given parent.
   *
   * @param string $termName
   *   The name of the term to search.
   * @param int $parentTID
   *   The parent TID of the term to search for. Defaults to 0.
   *
   * @return int
   *   TID of the term if found, else NULL.
   */
  protected function findTerm(string $termName, $parentTID = 0) {
    $query = $this->database->select('taxonomy_term_field_data', 'ttfd')
      ->fields('ttfd', ['tid']);
    $query->innerJoin('taxonomy_term__parent', 'tth', 'ttfd.tid = tth.entity_id');
    $query->condition('ttfd.vid', $this->configuration['bundle']);
    $query->condition('ttfd.name', $termName);
    $query->condition('tth.parent_target_id', $parentTID);
    return $query->execute()->fetchField();
  }

  /**
   * Create the term.
   *
   * @param array $value
   *   Value to use in creation of term.
   *
   * @return int
   *   The entity id of the generated term.
   */
  protected function createTerm(array $value) {
    $term = $this->entityTypeManager->getStorage('taxonomy_term')
      ->create($value);
    $term->save();
    return $term->id();
  }

}
