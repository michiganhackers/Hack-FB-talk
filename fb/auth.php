<?php

/*
 *  AUTH.PHP
 *
 *  Code to authenticate a user using facebook.
 *  Grabs appId and appSecret from config.php which is not included in repo
 *  config.php only includes definitions of those two variables.
 *  go to https://developers.facebook.com/apps to find these values
 *
 */

require_once 'facebook.php';
require_once 'config.php';

$facebook = new Facebook(array(
  'appId' => $appId,
  'secret' => $appSecret
));
$user = $facebook->getUser();

$params = array('redirect_uri' => 'http://hackers.gauravkulkarni.com',
                'scope' => 'user_status, user_photos, friends_photos, user_events, friends_events');

if($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch(FacebookApiException $e) {
    error_log($e);
    $user = null;
    $loginUrl = $facebook->getLoginUrl($params);
  }
} else {
  $loginUrl = $facebook->getLoginUrl($params);
}

?>
