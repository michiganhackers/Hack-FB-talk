<?php

/*
 *  STATUS.PHP
 *
 *  The page shows your "most popular" status messages
 *  It grabs the last 100 status messages and sorts them
 *  based on a score calculated using the following formula
 *  {comment_count} * 1.5 + {like_count}
 *
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/fb/auth.php';

// If there is no logged in user, redirect to login.php
if(!$user)
  header("Location: /login.php");

function compare_statuses($status1, $status2) {
  // Grab like and comment counts for both statuses
  $like_count1 = get_response_count($status1, "likes");
  $comment_count1 = get_response_count($status1, "comments");
  $like_count2 = get_response_count($status2, "likes");
  $comment_count2 = get_response_count($status2, "comments");

  // Calculate scores based on 1.5 * comment + like
  $score1 = (1.5 * $comment_count1) + $like_count1;
  $score2 = (1.5 * $comment_count2) + $like_count2;

  // Compare the two scores
  if($score1 == $score2) return 0;
  return ($score1 < $score2) ? +1 : -1;
}

function get_response_count($status, $type) {
  global $facebook;
  $response_count = count($status[$type]["data"]);
  // If response count == 25, most likely there are more. Fetch 101 of them
  // and display 100+ if we get 101.
  if($response_count == 25) {
    $responses = $facebook->api("/".$status["id"]."/".$type, "GET", array("limit"=>"101"));
    $response_count = count($responses["data"]);
    if($response_count == 101) { $response_count = "100+"; }
  }
  return $response_count;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" type="text/css" href="/assets/bootstrap.min.css" />
</head>
<body>
  <div class="container" style="margin-top: 20px">
  <?php
    // Fetch the user's last 100 statuses
    $statuses = $facebook->api('/'.$user.'/statuses', "GET", array("limit"=>"100"));
    $statuses = $statuses["data"];

    // Sort the statuses using our custom ranking function
    usort($statuses, compare_statuses);

    // Print each status message
    foreach($statuses as $status) {
      $like_count = get_response_count($status, "likes");
      $comment_count = get_response_count($status, "comments");

      // Only display status messages that have gotten at least 2 likes or comments
      if($like_count > 1 || $comment_count > 1) {
        echo "<div class='well'>";
        echo "<b>".$status["message"]."</b><br/>";
        if($like_count)
          echo $like_count." liked this<br/>";
        if($comment_count)
          echo $comment_count." commented on this<br/>";
        echo "</div>";
      }
    }
  ?>
  </div>
</body>
</html>
