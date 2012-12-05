<?php

/**
 * @file
 * Check if consumer token is set and if so send user to get a request token.
 */

/**
 * Exit with an error message if the CONSUMER_KEY or CONSUMER_SECRET is not defined.
 */
require_once('config.php');
if (CONSUMER_KEY === '' || CONSUMER_SECRET === '') {
  echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
  exit;
}

/* Build an image link to start the redirect process. */
$content = '<a href="./redirect.php"><img src="./images/lighter.png" alt="Sign in with Twitter"/></a>';

$body="";

$body.='<h2 id="title">Hello, Late Night Hashtagger!</h2><p>Please let this app use your Twitter credentials to access tweets.</p>';

if (isset($menu)) { $body.=$menu; }
if (isset($status_text)) { $body.='<h3>'.$status_text.'</h3>'; }
$body.='<p>'.print_r($content, true).'</p>';

/* Include HTML to display on the page. */
include('../html.php');

?>