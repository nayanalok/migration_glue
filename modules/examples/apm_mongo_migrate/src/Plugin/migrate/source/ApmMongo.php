<?php
namespace Drupal\apm_mongo_migrate\Plugin\migrate\source;

use Drupal\Core\Site\Settings;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateException;
use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for the MongoDB.
 *
 * @MigrateSource(
 *   id = "apm_mongo"
 * )
 */
class ApmMongo extends SourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * MongoDB database connection info.
   *
   * @var array
   */
  protected $mongoDBConnection;

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
    // If no mongodb connection info provided.
    if (empty(Settings::get('mongodb'))) {
      throw new MigrateException('MongoDB connection info is missing. Please see `READEME.md` to setup mongodb connection.');
    }
    $this->mongoDBConnection = Settings::get('mongodb');
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
    $summary[] = 'Alias: ' . $this->mongoDBConnection['database'];
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
        'description' => 'MongoDB ID field.',
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
        ->selectCollection($this->mongoDBConnection['database'], $this->sourceCollection)
        ->find([])
        ->toArray();
      return $this->getDataAsArray($rows);
    }
    catch (ConnectionTimeoutException $e) {
      $this->messenger()->addError($this->t('Unable to connect to mongodb server. Please check connection info.'));
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
   * Function to get data as array.
   *
   * @param mixed $data
   *   Array / Object / Final value.
   *
   * @return array
   *   Array of records.
   */
  protected function getDataAsArray($data) {
    $records = [];

    if (!empty($data)) {
      foreach ($data as $key => $value) {
        if ($value instanceof BSONDocument) {
          $records[$key] = (array) $value->jsonSerialize();
        }
      }
    }

    return $records;
  }

  /**
   * Create and get mongodb connection object.
   *
   * @return Client
   *   Mongo client object.
   */
  protected function getDatabase() {
    $connection_info = $this->mongoDBConnection['default'];
    return new Client($connection_info['uri'], $connection_info['uriOptions'], $connection_info['driverOptions']);
  }

}
