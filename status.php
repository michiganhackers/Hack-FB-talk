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
  // Calculate scores based on 1.5 * comment + like
  $score1 = (1.5 * $status1["comment_count"]) + $status1["like_count"];
  $score2 = (1.5 * $status2["comment_count"]) + $status2["like_count"];

  // Compare the two scores
  return $score2 - $score1;
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

    // Add like/comment count to each status
    foreach($statuses as &$status) {
      $status["like_count"] = get_response_count($status, "likes");
      $status["comment_count"] = get_response_count($status, "comments");
    }

    // Sort the statuses using our custom ranking function
    usort($statuses, compare_statuses);

    // Print each status message
    echo 
      "<div class='page-header'>
        <h1>
          Your most popular statuses
          <small>as determined mostly arbitrarily</small>
        </h1>
      </div>";


    foreach($statuses as $status) {
      // Only display status messages that have gotten at least 2 likes or comments
      if($status["like_count"] > 1 || $status["comment_count"] > 1) {
        echo "<div class='well'>";
        echo "<b>".$status["message"]."</b><br/>";
        if($status["like_count"])
          echo $status["like_count"]." liked this<br/>";
        if($status["comment_count"])
          echo $status["comment_count"]." commented on this<br/>";
        echo "</div>";
      }
    }
  ?>
  </div>
</body>
</html>
