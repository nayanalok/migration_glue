# Mysql Migrate

This is a sample module to migrate data from mysql database to drupal 8.

## Installation

* Enable this module 'apm_mysql_migrate'.
* Import mysql database from apm_mysql_migrate/data/mysql_custom_db.sql to your database.
* Update settings.php file with the migrate database details. For example:
```
$databases['migrate']['default'] = array (
  'database' => 'custom_mysql_db',
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