<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Migrates the youtube URL to youtube field.
 *
 * This requires https://www.drupal.org/project/youtube module.
 *
 * Example of migration yml.
 *
 *  field_youtube_video:
 *    plugin: apm_youtube_video
 *    source: video
 *
 * Example XML data:
 *
 * <video>http://www.youtube.com/watch?v=1SqBdS0XkV4</video>
 *
 * @MigrateProcessPlugin(
 *   id = "apm_youtube_video"
 * )
 */
class ApmYoutubeVideo extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * ApmYoutubeVideo constructor to check for required module.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param $pluginId
   *   The plugin_id for the plugin instance.
   * @param $pluginDefinition
   *   The plugin implementation definition.
   * @param ModuleHandlerInterface $moduleHandler
   *   Module handler to check if module exist.
   * @throws MigrateException
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    // If 'youtube' module is enabled.
    if (!$moduleHandler->moduleExists('youtube')) {
      throw new MigrateException('`youtube` module is not enabled.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $video_id = youtube_get_video_id($value);
    $processed_data = [];
    if ($video_id) {
      $processed_data['input'] = $value;
      $processed_data['video_id'] = $video_id;
    }
    return $processed_data;
  }

}
