# Journal Transporter plugin for OJS

An OJS plugin for the Journal Transporter. At present this plugin only provides API endpoints to export data from OJS.

```
  ___________________
 /,-----------------.\
||        .^.        ||
||      .'   `.      ||
||    .'   _   `.    ||
||  .'    (_)    `.  ||
||.'    .--~--.    `.||
|| ~~|  `-----'  |~~ ||
||   | |~|~~~|~| |   ||
||   |  `~~~~~'  |   ||
||   |  \|~|~|/  |   ||
|| __|   == ==   |__ ||
||`.     -- --     .'||
||  `.   -- --   .'  ||
||    `.       .'    ||
||      `.   .'      ||
||        `.'        ||
|\___________________/|
 `-------------------'
```
ASCII art from https://ascii.co.uk/art/startrek

# Requirements

This plugin has only been tested with a modified version of OJS 2.3.6 running PHP 7.2. This does require modifications
to OJS 2.x to make it compatible with PHP 7.x. 

# Overview

This plugin provides HTTP endpoints for fetching data out of OJS. Access to these endpoints can be authorized via basic
auth, or site admin authorization, or both. Data is mapped from OJS data objects to associative arrays which are, in 
turn, transformed into JSON for output.

# API

All API endpoints will look like this: [SITE DOMAIN]/index.php/pages/jt/api/[ROUTE]. The allowed routes are
defined in `Classes/Api/Controller.php`. Routes that display single records (journals, articles, etc), accept a URL
parameter `?debug`, which dumps all data from the requested object rather than mapping specific fields.

## Authentication

This snippet needs to be added to the config.inc.php file:

```
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Journal Transporter Plugin ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[journaltransporter]

enable_plugin_endpoints = On
require_basic_auth = Off
basic_auth_user =
basic_auth_password =
require_site_admin = Off
```

By default, this will enable the API routes for unauthenticated requests, so be careful and don't do this in a production
instance! Basic auth and site admin authentication can be enabled individually. If basic auth is enabled, the username
and password must be provided, or access will be denied.

## Routes

Routes are defined with regexes in `Classes/Api/Controller.php`. Named parameters in the regex help make keeping track
of dynamic parameters in the route easier. Most routes have their own controller, but some share a list / single
controller.

## Data Object Mapping

All mapping of data objects to JSON is defined in the classes located in `Classes/Builder/Mapper/DataObject`. At its
core, this consists of a mapping configuration, but `preMap()` and  `postMap()` methods are available to process the 
data object before and after mapping.

Different contexts can be defined in the children of the `AbstractDataObjectMapper` alongside the mapping. Contexts
allow certain mapped fields to be excluded or included in the constructed data. By default, all fields are included
in the data. If fields are explicitly set to be `include`d, then will appear. If they are explicitly set to be 
`exclude`d, or the `exclude` value is set to `*` and they aren't `include`d, they will not appear.
See `Classes/Builder/Mapper/DataObject/Journal.php` for an example.

Contexts allow us to use the same mapping configuation for lists of items and singular display of the same items. See

## License

This software is licensed with the 3-clause BSD license.