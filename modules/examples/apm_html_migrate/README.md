# APM HTML Migrate

This is a sample module to migrate data from HTML file or web URL to drupal 8.

## Installation

* Enable this module 'apm_html_migrate'.
* While writing HTML migration yml file, use selector_type as one of the HTML id or class or tag.
* And selector as the name of id or class or tag. For eg: for tag you can specify h1 as selector.
* Make sure the selector you are specifying should be unique, otherwise it will pull html for the first occurrence only. 
* Execute the migration with above considerations.
