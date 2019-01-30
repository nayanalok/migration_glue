# APM Drupal 8 to Drupal 8 Migrate

This is a sample module to migrate data from Drupal 8 to drupal 8.

## Installation

* Enable this module 'apm_d8_to_d8_migrate'.
* Update settings.php file with the migrate database details. For example:
```
$databases['migrate']['default'] = array (
  'database' => 'd8_source',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
 ```
 * And execute the migration.