<?php

namespace Drupal\migration_glue\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Replace a patterned specified xpath string in the source.
 *
 * For a given xpath pattern, source string will be replaced by the given
 * replacement string.
 *
 * @code
 * process:
 *   type:
 *     plugin: xpath_replace_pattern
 *     xpath: "//body[contains(text(), "google")]"
 *     replace: "value to be replaced, it could be empty string."
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "xpath_replace_pattern"
 * )
 */
class XpathReplacePattern extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    if (empty($this->configuration['xpath'])) {
      throw new MigrateException('You need to specify the "xpath" config on the plugin.');
    }

    if (!isset($this->configuration['replace'])) {
      throw new MigrateException('You need to specify the "replace" config on the plugin.');
    }

    $value = $this->xpathProcessReplace($value);

    return $value;

  }

  /**
   * Replaces in the source string for given xpath pattern.
   *
   * @param string $string
   *   Source string.
   * @return string
   *   Replaced string.
   */
  protected function xpathProcessReplace(string $string) {
    $dom = new \DomDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($string);
    $xpath = new \DOMXpath($dom);
    /** @var \DOMNodeList */
    $nodes = $xpath->query($this->configuration['xpath']);
    foreach ($nodes as $node) {
      $dom->createDocumentFragment();
      $newelement = $dom->createTextNode($this->configuration['replace']);
      $node->parentNode->replaceChild($newelement, $node);
    }

    $html = $dom->saveHTML();
    // Get only body part as currently $html contains header/html and other tags.
    preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $matches);

    if (!empty($matches[1])) {
      return $matches[1];
    }

    return $string;
  }

}
