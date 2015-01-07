DapperDan
=========

[![Build Status](https://travis-ci.org/TrexlerLibrary/DapperDan.svg)](https://travis-ci.org/TrexlerLibrary/DapperDan)

a reconfiguration of our single-purpose LDAP authentication script

## usage

```php
$dan = new \DapperDan\server('ldap.example.com');
$results = $dan->search(array("sn" => "Lincoln", "fn" => "Abbey"));
// returns an array of DapperDan\People objects

// -- or -- //

$logged_in = $dan->login(array("dn" => "a=AbbeyLincoln,b=Chicago,c=Vocalist"), "password");

// ~~~~~~ //

$email = $results[0]->get('email');
// returns abbey@bluenote.com
```

## license

MIT