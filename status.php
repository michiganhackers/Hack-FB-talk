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

require_once 'fb/auth.php';

// If there is no logged in user, redirect to login.php
if(!$user)
  header("Location: login.php");

function compare_statuses($status1, $status2) {
  // Calculate scores based on 1.5 * comment + like
  $score1 = (1.5 * $status1["comment_count"]) + $status1["like_count"];
  $score2 = (1.5 * $status2["comment_count"]) + $status2["like_count"];

  // Compare the two scores
  return $score2 - $score1;
}

function get_response_count($status, $type) {
	global $facebook;
  $max_returned = 25; // The maximum number of likes fb returns by default

  $response_count = count($status[$type]["data"]);
  // If response count == $max_returned, most likely there are more. Fetch 100 of them
  // and display 100+ if we get 100.

  if($response_count == $max_returned) {
    $responses = $facebook->api("/".$status["id"]."/".$type, "GET", array("limit"=>"100"));
    $response_count = count($responses["data"]);
    if($response_count == 100) { $response_count = "100+"; }
  }
  return $response_count;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" type="text/css" href="assets/bootstrap.min.css" />
</head>
<body>
  <div class="container" style="margin-top: 20px">
    <div class='page-header'>
      <h1>
        Your most popular statuses
        <small>as determined mostly arbitrarily</small>
      </h1>
    </div>
  
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
    foreach($statuses as $status) {
      // Only display status messages that have gotten at least a certain number of likes/comments

      if($status["like_count"] > 2 || $status["comment_count"] > 1) {
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
