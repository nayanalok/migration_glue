# CouchDB Migrate

This is a sample module to migrate data from CouchDB to Drupal8.

## Requirements

* This module requires php `php-on-couch` library to be installed.
* Run `composer require php-on-couch/php-on-couch` to install the library.

## Installation
* Once `php-on-couch` PHP library is installed, update the
settings.php with the `couchdb` database details like below -
```
$settings['couchdb'] = [
  // Connection info.
  'default' => [
    'dns' => 'http://localhost:5984',
    'database' => 'drupal8couch',
  ],
];
 ```
* Once DB settings done, import the data from the `data` directory
of this module to your mongodb database by following steps:
    * Navigate to `http://127.0.0.1:5984/_utils/` and create database `drupal8couch`.
    * Inside the database create 3 documents with _id content, tags and users.
    * Now edit the documents and add json data from the corresponding `data` directory files.
* Then enable the module `apm_couch_migrate` and run migration.
