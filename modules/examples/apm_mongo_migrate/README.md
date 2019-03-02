# MongoDB Migrate

This is a sample module to migrate data from MongoDB to Drupal8.

## Requirements

* This module requires php `mongodb` extension to be enabled.
* Follow/See `http://php.net/manual/en/set.mongodb.php` to
install the `mongodb` php extension.
* Once extension is installed, verify that extension is available
by checking `php -i | grep 'mongo'` or just visiting phpinfo() page.
* Once extension is installed, need to install the `mongodb` PHP
library as well.
* Follow/See `http://php.net/manual/en/mongodb.tutorial.library.php`
to install mongodb php library.

## Installation
* Once `mongodb` extension and PHP library is installed, update the
settings.php with the `mongodb` database details like below -
```
$settings['mongodb'] = [
  // Connection info.
  'default' => [
    'uri' => 'mongodb://127.0.0.1:27017',
    'uriOptions' => [],
    'driverOptions' => [],
  ],
  // Database name.
  'database' => 'mongodb database name here',
];
 ```
* Once DB settings done, import the data from the `data` directory
of this module to your mongodb database by command -
`./mongorestore -d <database_name> <data_directory_path>`
* Command to export mongodata -
`./mongodump -d <database_name> -o <directory_backup>`
* Then enable the module `apm_mongo_migrate` and run migration.