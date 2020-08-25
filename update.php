<?php

error_reporting(E_ALL);
@ini_set("display_errors", 1);  /* don't show errors onscreen */

/* Config */
include_once("../../private/config.php");
include_once("../../private/functions.php");

// Custom error handler that writes to a file
set_error_handler("dyndns_error_handler");

/* Opts */
$pass = isset($_GET['pass']) ? $_GET['pass'] : null;
$domain = isset($_GET['domain']) ? $_GET['domain'] : null;
$ip = isset($_GET['ip']) ? $_GET['ip'] : null;

if (!validCred($pass)) {
    trigger_error("bad credentials", E_USER_WARNING);
    respond("failed", "bad credentials");
}
if (isset($ip) && !validIP($ip)) {
    trigger_error("not a valid ip address", E_USER_WARNING);
    respond("failed", "not a valid ip address");
}

trigger_error("Updating $domain to new IP $ip", E_USER_NOTICE);
nsupdate($keyfile, $dnsserver, $zone, $domain, $ip);
echo "good"
?>