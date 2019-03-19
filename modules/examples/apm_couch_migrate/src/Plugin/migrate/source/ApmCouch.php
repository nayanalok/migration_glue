<?php
namespace Drupal\apm_couch_migrate\Plugin\migrate\source;

use Drupal\Core\Site\Settings;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateException;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNoResponseException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for the CouchDB.
 *
 * This source plugin requires two configurations `collection` and `fields`.
 * Collection will be the document from the CouchDB while fields are field keys
 * in the CouchDB document.
 *
 * Example:
 * @code
 * source:
 *   plugin: apm_couch
 *   collection: content #[Required]
 *   fields:
 *     title: 'Content title'
 *     content: 'Content body'
 *     author: "Author who created the content"
 *     tags: "Tags associated with content"
 * @endcode
 *
 * @MigrateSource(
 *   id = "apm_couch"
 * )
 */
class ApmCouch extends SourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * CouchDB database connection info.
   *
   * @var array
   */
  protected $couchDBConnection;

  /**
   * Source DB alias.
   *
   * @var string
   */
  protected $sourceCollection;

  /**
   * Source fields.
   *
   * @var array
   */
  protected $sourceFields;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    // @see settings.php for connection info.
    // If no couchdb connection info provided.
    if (empty(Settings::get('couchdb'))) {
      throw new MigrateException('CouchDB connection info is missing. Please see `READEME.md` to setup couchdb connection.');
    }
    $this->couchDBConnection = Settings::get('couchdb');
    $this->sourceCollection = $configuration['collection'];
    $this->sourceFields = $configuration['fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return $this->sourceFields;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    $summary = [];
    $summary[] = 'Alias: ' . $this->couchDBConnection['database'];
    $summary[] = 'Collection: ' . $this->sourceCollection;
    return implode(', ', $summary);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      '_id' => [
        'type' => 'string',
        'length' => 24,
        'not null' => TRUE,
        'description' => 'CouchDB ID field.',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    $source_data = $this->getSourceData();
    return new \ArrayIterator($source_data);
  }

  /**
   * @return mixed
   */
  protected function getSourceData() {
    try {
      $rows = $this->getDatabase()
        ->asArray()
        ->getDoc($this->sourceCollection)['rows'];
      return $rows;
    }
    catch (CouchNoResponseException $e) {
      $this->messenger()->addError($this->t('Unable to connect to couchdb server. Please check connection info.'));
      return [];
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error: @message', [
        '@message' => $e->getMessage(),
      ]));
      return [];
    }
  }

  /**
   * Create and get couchdb connection object.
   *
   * @return CouchClient
   *   Couch client object.
   */
  protected function getDatabase() {
    $connection_info = $this->couchDBConnection['default'];
    return new CouchClient($connection_info['dns'], $connection_info['database']);
  }

}
