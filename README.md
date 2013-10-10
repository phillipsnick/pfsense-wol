pfSenseWOL
===========

Introduction
------------

PHP library for calling pfSense's 2.1 WOL function using CURL.

My internal network makes use of multiple VLANs, there was no simply solution to get WOL working across multiple VLANs so decided to put this script together.


Installation
------------

Using Composer (recommended)
----------------------------

Add the following to your composer.json file

```json
{
    "require": {
        "phillipsnick/pfSenseWOL": "0.0.1"
    }
}
```

Then run `composer install`


Using Git submodules
--------------------

    git submodule add https://github.com/phillipsnick/pfSenseWOL.git 


Usage
-----

```php
$service = new \Pfsensewol\Wol(array(
    'https' =>      true,
    'pfsense' =>    'pfsense.hostname',
    'username' =>   'pfSenseUsername',
    'password' =>   'pfSensePassword'
));

$service->send('MacAddress', 'opt4');
```

Or see example inside Examples/SingleHost.php

Notes
-----

This has only been tested using pfSense 2.1, included are the necessary checks to work with the CSRF protection. 
Feel free to help improve this! 