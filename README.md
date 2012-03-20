Michigan Hackers: Hack FB
=========================

Code for the upcoming Michigan Hackers talk on the FB API
Uses the php sdk to pull and manipulate various sets of data from Facebook

Status.php
----------
Does a basic open graph query to pulls your last 100 statuses and ranks them by "popularity"
Popularity score is defined as (1.5 * {comment count}) + {like count}

Photo.php
---------
Uses FQL to get a list of photos you are tagged in
It then computes the average number of likes on those photos
As well as shows the 3 photos you are tagged in that has the most likes

Event.php
---------
Uses FQL multiquery to first grab all of your friends, then all the ids of events they are attending and finally information about those events (name and picture)
Displays the 10 events that have the most number of friends who have responded attending.
