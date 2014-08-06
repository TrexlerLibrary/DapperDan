DapperDan
=========

**WIP**: _will_ very likely break at this point

a reconfiguration of our single-purpose LDAP authentication script

## usage

```php
$dan = new \DapperDan\server('ldap.example.com');
$results = $dan->search(array("sn" => "Lincoln", "fn" => "Abbey"));
// returns an array of User objects

// -- or -- //

$logged_in = $dan->login(array("dn" => "a=AbbeyLincoln,b=Chicago,c=Vocalist"), "password");
// returns
```

$email = $results[0]->getValue('email');
// returns abbey@bluenote.com