<?php

//Build NS-Update
function nsupdate($keyfile, $dnsserver, $zone, $domain, $ip) {
    // prepare command
    $data = "<<EOF
    server $dnsserver
    zone $zone
    update delete $domain A
    update add $domain 10 A $ip
    send
    EOF";
    // run DNS update
    exec("/usr/bin/nsupdate -k $keyfile $data", $cmdout, $ret);
}

function validIP($ip) {
  if (filter_var($ip, FILTER_VALIDATE_IP)) {
    return true;
  }
  return false;
}

function validCred($pass) {
  if ($pass == REMOTE_PASS) {
    return true;
  }
  return false;
}

/* Respond with status & message in JSON */
function respond($status, $msg = "") {
  header('Content-type: application/json');
  $response = array();
  $response["status"] = $status;
  if (!empty($msg)) {
    $response["msg"] = $msg;
  }
  echo json_encode($response);
  exit();
}

/* Custom error handler to log to file */
function dyndns_error_handler($errno, $errstr, $errfile, $errline)
{
  if (!(error_reporting() & $errno)) {
    // This error code is not included in error_reporting
    return;
  }

  $date = date(DATE_W3C);
  $str = "";

  switch ($errno) {
  case E_USER_ERROR:
    $str .= "$date ERROR [$errno]: $errstr, Fatal error on line $errline in file $errfile";
    break;

  case E_USER_WARNING:
    $str .= "$date WARNING [$errno]: $errstr\n";
    break;

  case E_USER_NOTICE:
    $str .= "$date NOTICE [$errno]: $errstr\n";
    break;

  default:
    $str .= "$date Unknown error type: [$errno] $errstr\n";
    break;
  }

  file_put_contents(LOG, $str, FILE_APPEND);

  /* Don't execute PHP internal error handler */
  return true;
}

?>