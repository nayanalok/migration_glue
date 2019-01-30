<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Replaces a given html tag data having html attribute and value.
 *
 * This is used to replace a html tag having a specific attribute with
 * specific value by the given replacement string.
 *
 * Example:-
 * 1. `<pre class="test">*</pre>`
 *    With html tag `pre`, attribute name `class` and attribute_value `test`,
 *    replaces the complete string (along with html tag as well) with the
 *    given replacement string.
 * 2. `<span>*</span>`
 *    With html tag `span`, all `span` tags and their html content will be
 *    replaced by the given replacement string.
 *
 * @code
 * process:
 *   type:
 *     plugin: html_tag_str_replace
 *     html_tag: "span"
 *     attribute_name: "class"
 *     attribute_value: "test-class"
 *     replace: "replacement string"
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "html_tag_str_replace"
 * )
 */
class HtmlTagStrReplace extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    if (empty($this->configuration['html_tag'])) {
      throw new MigrateException('You need to specify the "html_tag" config on the plugin.');
    }

    if (!isset($this->configuration['replace'])) {
      throw new MigrateException('You need to specify the "replace" config on the plugin.');
    }

    if (!empty($this->configuration['attribute_name'])
      && !isset($this->configuration['attribute_value'])) {
      throw new MigrateException('You need to specify the "attribute_value" config on the plugin.');
    }
    else {
      $attribute_name = $this->configuration['attribute_name'];
      $attribute_value = $this->configuration['attribute_value'];
    }

    // <pre class="script-css">*</pre>
    // Sample pattern that will be built.
    // $pattern = '/' . '<pre class=[\"\']script-css[\"\'][^\<]*\<\/pre>' . '/';

    $html_tag = $this->configuration['html_tag'];

    // Prepare the pattern.
    $pattern_format = '/<' . $html_tag;

    // If html attribute name is available in plugin config.
    if (!empty($attribute_name)) {
      $pattern_format .= ' ' . $attribute_name . '=[\"\']' . $attribute_value . '[\"\']';
    }

    $pattern_format .= '[^\<]';
    $pattern_format .= '*';
    $pattern_format .= '\<\/' . $html_tag . '>/';

    $value = preg_replace($pattern_format, $this->configuration['replace'], $value);

    return $value;

  }

}
