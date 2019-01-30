# Acquia Platform Migration

## Installation
* Add below lines in `repositories` section of your `composer.json` file in your project root
    
    `"url": "https://github.com/joshirohit100/migration_glue.git",`
    
    `"type": "git"`
     
* Then run `composer require joshirohit100/migration_glue`
* `drush en -y acquia_platform_migration`


## Usage
* Visit `/admin/config/development/create-migration`
