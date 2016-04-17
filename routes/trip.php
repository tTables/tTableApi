<?php

$app->group('/trip', function() use($app) {
  $app->get('/', function() {
    echo 'NO!';
  });
  
  $app->get('/:line/:direction/:day/:trip', function($line, $direction, $day, $trip) use($app) {
    $trip = get_trip($line,$direction,$day,$trip);
    echo json_encode($trip);
  });
});