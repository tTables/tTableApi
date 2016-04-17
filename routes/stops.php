<?php

use RedBeanPHP\R;

$app->group('/stops', function() use($app) {
  $app->get('/', function() use($app) {
    $stops = get_stops_filter(null);
    $lines_from_stops = array();
    // foreach($stops as &$stop) {
      // $stop['lines'] = get_lines_from_stop($stop["id"])[0]['lines'];
    // }
    
    echo json_encode($stops);
  });
  
  $app->get('/:stop', function($stop) use($app) {
    $departures = [
      'departures' => [],
      'stop_name' => get_stop_name($stop)
    ];
    $daytypes = R::find('daytypes');
    foreach($daytypes as $daytype) {
      $temp = array(
        "daytype" => $daytype->name,
        "daytype_id" => $daytype->id,
        "departures" => get_stop_chrono_departures($stop, $daytype->id)
      );
      $departures['departures'][] = $temp;
    }
    
    echo json_encode($departures);
  });
  
  $app->get('/all/:stop', function($stop) use($app) {
    $departures = array();
    $lines_and_dirs = get_lines_and_directions_no_from_stop($stop);
    
    foreach($lines_and_dirs as $line) {
      $tmp = get_departures($line["line"], $line["dirnumber"], $stop);
      $departures[] = $tmp;
    }
    
    echo json_encode(array(
      'departures' => $departures,
      'stop_id' => $stop,
      'stop_name' => get_stop_name($stop)
    ));
  });
});