pfSenseWOL
===========

Introduction
------------

PHP library for calling pfSense's WOL function using CURL.

My internal network makes use of multiple VLANs, there was no simply solution to get WOL working across multiple VLANs so decided to put this script together.


Installation
------------

Using Composer (recommended)
----------------------------

```json
{
    "require": {
        "phillipsnick/pfSenseWOL": "0.0.1"
    }
}
```

Using Git submodules
--------------------

    git submodule add https://github.com/phillipsnick/pfSenseWOL.git 