<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\file\FileInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateException;
use Drupal\media_entity\Entity\Media;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Unicode;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Process plugin to create Media entity for the images in the source.
 *
 * @code
 * process:
 *   body/value:
 *     plugin: apm_media_entity
 *     source_folder: "" #[Required]
 *     destination_folder: "public://tag-images" #[Required]
 *     media_bundle: image #[Required]
 *     media_bundle_field: field_image #[Required]
 * @endcode
 *
 * XML source -
 * <body>HTML content for field.<img src="https://cdn.lynda.com/course/439683/439683-636441077028502313-16x9.jpg" alt="test" />
 * </body>
 *
 *
 * @MigrateProcessPlugin(
 *  id = "apm_media_entity"
 * )
 */
class ApmMediaEntity extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * User account service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * User account service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * ApmMediaEntity constructor to check for required fields.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param $pluginId
   *   The plugin_id for the plugin instance.
   * @param $pluginDefinition
   *   The plugin implementation definition.
   * @param ModuleHandlerInterface $moduleHandler
   *   Module handler to check if module exist
   * @param AccountProxyInterface $currentUser
   *   Get current active user
   * @param LanguageManagerInterface $languageManager
   *   The language manager
   * @throws MigrateException
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, ModuleHandlerInterface $moduleHandler, AccountProxyInterface $currentUser, LanguageManagerInterface $languageManager) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    // If 'media_entity' module is enabled.
    if (!$moduleHandler->moduleExists('media_entity')) {
      throw new MigrateException('`Media Entity` module is not enabled.');
    }

    // If `source_folder` and `destination_folder` not available in migration configuration.
    if (!isset($this->configuration['source_folder']) || empty($this->configuration['destination_folder'])) {
      throw new MigrateException('You need to specify the source and destination folder on the plugin.');
    }
    // If `media_bundle` and `media_bundle_field` not available in migration configuration.
    if (empty($this->configuration['media_bundle']) || empty($this->configuration['media_bundle_field'])) {
      throw new MigrateException('You need to specify media bundle and image field from media bundle on the plugin.');
    }

    $this->currentUser = $currentUser;
    $this->languageManager = $languageManager->getDefaultLanguage();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('module_handler'),
      $container->get('current_user'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($html, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Get all the image tags from source.
    preg_match_all('/<img[^>]+>/i', $html, $result);
    if (!empty($result[0])) {
      foreach ($result as $img_tags) {
        foreach ($img_tags as $img_tag) {
          preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $tag_attributes);

          // Check if image src is provided.
          if (empty($tag_attributes[2][0])) {
            continue;
          }

          // Check if image path is present.
          $filepath = str_replace('"', '', $tag_attributes[2][0]);
          // Create file object from a locally copied file.
          $filename = basename($filepath);

          // Check if directory exists and is writable.
          $destination = $this->configuration['destination_folder'];
          if (!file_prepare_directory($destination, FILE_CREATE_DIRECTORY)) {
            continue;
          }

          $images_source = $this->configuration['source_folder'];
          $file_contents = filter_var($filepath, FILTER_VALIDATE_URL)
            ? file_get_contents($filepath)
            : ((!empty($images_source)) ?
              file_get_contents($images_source . $filepath) :
              file_get_contents($filepath));

          $new_destination = $destination . '/' . $row->getSourceProperty('id') . '-' . $filename;
          // If file contents are empty
          if (empty($file_contents)) {
            continue;
          }

          // Save file on destination.
          if ($file = file_save_data($file_contents, $new_destination, FILE_EXISTS_REPLACE)) {
            $media_bundle_field = $this->configuration['media_bundle_field'];
            // Create media entity using saved file.
            $this->createMediaEntity($this->configuration['media_bundle'], $media_bundle_field, $file, $tag_attributes);
            // Get uuid from file.
            $uuid = $this->getMediaUuid($file, $media_bundle_field);

            //Replace image in the source with drupal entity.
            $replace_image = (!empty($uuid)) ?
              '<p><drupal-entity
              data-embed-button="embed_image" 
              data-entity-embed-display="entity_reference:media_thumbnail"
              data-entity-embed-display-settings=""
              data-entity-type="media"
              data-entity-uuid="' . $uuid . '"></drupal-entity> </p>' : '';
            $html = str_replace($img_tag, $img_tag. $replace_image, $html);
          }
        }
      }
    }
    return $html;
  }

  /**
   * Helper function to create media entity for source image.
   *
   * @param string $media_bundle
   *   Name of the media bundle
   * @param string $media_bundle_field
   *   Name of the field from media bundle
   * @param object $file
   *   File object for media entity
   * @param array $tag_attributes
   *   Image attributes
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createMediaEntity($media_bundle, $media_bundle_field, $file, $tag_attributes) {
    $media = Media::create([
      'bundle'      => $media_bundle,
      'uid'         => $this->currentUser->id(),
      'langcode'    => $this->languageManager->getId(),
      'status'      => Media::PUBLISHED,
      $media_bundle_field => [
        'target_id' => $file->id(),
        'alt'       => !empty($tag_attributes[2][1]) ? Unicode::truncate(str_replace('"', '', $tag_attributes[2][1]), 512) : '',
        'title'     => !empty($tag_attributes[2][1]) ? Unicode::truncate(str_replace('"', '', $tag_attributes[2][1]), 1024) : '',
      ],
    ]);
    $media->save();
  }

  /**
   * Get uuid from File provided.
   *
   * @param FileInterface $file
   *   File interface for which uuid is required
   * @param string $media_bundle_field
   *   Name of the field from media bundle
   * @return mixed
   *   uuid for File provided.
   */
  protected function getMediaUuid(FileInterface $file, $media_bundle_field) {
    $connection = \Drupal::database();
    $query = $connection->select('media__' . $media_bundle_field, 'f');
    $query->innerJoin('media', 'm', 'm.mid = f.entity_id');
    $query->fields('m', ['uuid']);
    $query->condition('f.' . $media_bundle_field . '_target_id', $file->id());
    $uuid = $query->execute()->fetchField();
    return $uuid;
  }
}
