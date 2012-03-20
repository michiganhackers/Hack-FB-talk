<?php

/*
 *  EVENT.PHP
 *
 *  This page shows you upcoming events your friends are going to. 
 *  Events are ranked by the number of friends attending.
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
    <div class='page-header'>
      <h1>
        Upcoming Events
        <small>Drink Responsibly</small>
				<!-- and with friends! -->
			</h1>
    </div>
  
    <?php

      // Do a batch query to grab events
      $multiquery = '{
        "getFriends":"SELECT uid2 FROM friend WHERE uid1 = me()",
        "getEventIDs":"SELECT eid FROM event_member WHERE uid in (SELECT uid2 FROM #getFriends) AND rsvp_status=\'attending\' AND start_time > '.time().'",
        "getEventNames":"SELECT pic_small, eid, name FROM event WHERE eid in (SELECT eid FROM #getEventIDs)",
      }';
      $results = $facebook->api(array('method' => 'fql.multiquery',
                                      'queries' => $multiquery));

      // Populate associated arrays that map ids to names and img_urls
      $events = $results[2]['fql_result_set'];
      foreach($events as $event) {
        $event_names[$event['eid']] = $event['name'];
        $event_pics[$event['eid']] = $event['pic_small'];
      }

      // Populate an array of event ids that friends are attending
      $members = $results[1]['fql_result_set'];
      foreach($members as $member) {
        $event_members[] = $member['eid'];
      }

      // Get the count of each element and sort to get the events most friends are attending
      $member_counts = array_count_values($event_members);
      arsort($member_counts);

      // Grab the 10 events most friends are attending
      $top_event_ids = array_slice($member_counts, 0, 10, true);

      // Display each event
      foreach($top_event_ids as $event_id => $count) {
        echo "
          <a href='http://facebook.com/".$event_id."'>
            <div class='well'>
              <img src='".$event_pics[$event_id]."' />
              <span>".$count." friends are attending ".$event_names[$event_id]."</span>
            </div>
          </a><br/>";
      }

    ?>

  </div>
</body>
</html>
