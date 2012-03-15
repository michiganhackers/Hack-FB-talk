<?php

/*
 *  PHOTO.PHP
 *
 *  The page shows the average number of likes of photos you are tagged in
 *  It uses fql to do so.
 *
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/fb/auth.php';

// If there is no logged in user, redirect to login.php
if(!$user)
  header("Location: /login.php");

// given a like_info object, grab the like_count
function get_like_count($like_info) {
  return $like_info["like_info"]["like_count"];
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
    // Perform a query that pulls like info and img src 
    // from photos the user is tagged in.
    $fql_query = 
      "SELECT like_info, src_big FROM photo where object_id in 
        (SELECT object_id FROM photo_tag where subject=$user)";
    $photos = $facebook->api(array('method' => 'fql.query', 
                                   'query' => $fql_query));

    /* Get the average number of likes */

    // $likes is an array of like count. We take out all the 0 likes from $likes
    $likes = array_map("get_like_count", $photos);
    $likes = array_filter($likes, create_function('$likes', 'return $likes > 0;'));

    // Compute the average number of likes
    $average_like = array_sum($likes) / count($likes);
    
    // Print out the average number of likes
    echo 
      "<div class='page-header'>
        <h1>
          On average, ".round($average_like, 2)." people like 
          photos you are tagged in
          <small>Congratulations</small>
        </h1>
      </div>";


    /* Get most liked photos */

    // Sort photos based on like count.
    usort($photos, 
          create_function(
            '$photo1, $photo2', 
            'return get_like_count($photo2) - get_like_count($photo1);'));

    // Grab the 3 most liked photos
    $popular_photos = array_slice($photos, 0, 3);

    // Display the 3 most liked photos
    echo "<h3>Photos you're in that have the most likes:</h3><br/>
          <ul class='thumbnails'>";
    foreach($popular_photos as $popular_photo) {
      echo "<li class='span4'>
              <div class='thumbnail'>
                <img src=".$popular_photo['src_big']."/>
                <div class='caption'>
                ".get_like_count($popular_photo)." people liked this<br/>
              </div>
            </li>";
    }
    echo "</ul>";
  ?>
  </div>
</body>
</html>
