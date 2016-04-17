<?php

use RedBeanPHP\R;

$app->group('/departures', function() use($app) {
  $app->get('/:line/:direction/:stop', function($line,$direction,$stop) use ($app) {        
    $departures = get_departures($line, $direction, $stop);
    $departures['stop_name'] = get_stop_name($stop);

    echo json_encode($departures);  
  });
});