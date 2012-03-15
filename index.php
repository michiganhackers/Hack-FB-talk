<?php

/*
 *  INDEX.PHP
 *  
 *  Landing page of the site. Provides links to go to each feature built
 *
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/fb/auth.php';

// If there is no logged in user, redirect to login.php
if(!$user) 
  header("Location: /login.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" type="text/css" href="/assets/bootstrap.min.css" />
</head>
<body>
  <div class="container" style="margin-top: 20px">
    <h3>You are signed in!</h3><br/>
    <a class='btn' href='/status.php'>Your top statuses</a>
  </div>
</body>
</html>
