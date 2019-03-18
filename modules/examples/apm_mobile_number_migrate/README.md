# Mobile Number migration

This is a sample module to migrate mobile number data to Mobile Number field of Drupal 8.
This example module is build on top of contrib module 'mobile_number' and it adds a Mobile number field in user entity.
2 sample migration (XML and JSON) has been added to demonstrate the working of mobile number migration.

## Installation

* Enable this module 'apm_mobile_number_migrate'.
* Goto 'admin/config/people/accounts/form-display' and move 'Mobile Number' field out from disabled fields.
* Run 'User Mobile XML Migrate' migration.
* Run 'User Mobile JSON Migrate' migration.