<?php

namespace Drupal\apm_html_migrate\Plugin\migrate\source;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Source for HTML.
 *
 * If the HTML file contains non-ASCII characters, make sure it includes a
 * UTF BOM (Byte Order Marker) so they are interpreted correctly.
 *
 * @MigrateSource(
 *   id = "apm_html_migrate"
 * )
 */
class HTML extends SourcePluginBase {

  /**
   * The source URLs to retrieve.
   *
   * @var array
   */
  protected $sourceUrls = [];

  /**
   * List of available source fields.
   *
   * Keys are the field machine names as used in field mappings, values are
   * descriptions.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * List of key fields, as indexes.
   *
   * @var array
   */
  protected $keys = [];

  /**
   * List of key Ids, as indexes.
   *
   * @var array
   */
  protected $ids = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    if (!is_array($configuration['urls'])) {
      $configuration['urls'] = [$configuration['urls']];
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->fields = $configuration['fields'];
    $this->sourceUrls = $configuration['urls'];
    $this->ids = $configuration['ids'];
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    // This could cause a problem when using a lot of urls, may need to hash.
    $urls = implode(', ', $this->sourceUrls);
    return $urls;
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    return $this->getDataParserPlugin();
  }

  /**
   * Returns the initialized data parser plugin.
   *
   * @return \Drupal\migrate_plus\DataParserPluginInterface
   *   The data parser plugin.
   */
  public function getDataParserPlugin() {
    // Logic to parse HTML.
    $arr_data = [];
    if (class_exists('DOMDocument')) {
      $dom = new \DOMDocument;
      foreach ($this->sourceUrls as $key => $url) {
        $html = file_get_contents($url);
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        // Create array of HTML data.
        foreach ($this->fields as $field) {
          if ($field['selector_type'] == 'id') {
            $arr_data[$key][$field['name']] = $dom->saveHTML($dom->getElementById($field['selector']));
          }
          elseif ($field['selector_type'] == 'tag') {
            $arr_data[$key][$field['name']] = $dom->saveHTML($dom->getElementsByTagName($field['selector'])->item(0));
          }
          elseif ($field['selector_type'] == 'class') {
            $xpath = new \DOMXPath($dom);
            $innerHTML = '';
            $classname = $field['selector'];
            $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
            $tmp_dom = new \DOMDocument();
            foreach ($nodes as $node) {
              $tmp_dom->appendChild($tmp_dom->importNode($node, true));
            }
            $innerHTML .= trim($tmp_dom->saveHTML());
            $arr_data[$key][$field['name']] = $innerHTML;
          }
        }
        $arr_data[$key]['path'] = $url;
      }
    }
    else {
      \Drupal::messenger()->addMessage(t('Enable PHP extension for DOMDocument.'), 'error');
    }

    return new \ArrayIterator($arr_data);
  }

  /**
   * {@inheritdoc}
   */
  public function getIDs() {
    return $this->ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [];
    foreach ($this->fields as $field_info) {
      $fields[$field_info['name']] = isset($field_info['label']) ? $field_info['label'] : $field_info['name'];
    }
    return $fields;
  }
}
