<?php

$dnsserver = "ns.example.com";
$zone = "dyn.example.com";
$keyfile = "/var/lib/bind/keys.conf";

// Path to logs (write access needed)
define('LOG', '../../private/dyndns.log');

// Credentials to access this service
define('REMOTE_PASS', 'SUPER_SECRET_PASSWORD');

?>
