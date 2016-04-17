<?php

use RedBeanPHP\R;

$app->group('/lines', function() use($app) {
  $app->get('/', function() use($app) {
    $lines = R::getAll('SELECT * FROM `lines` ORDER BY line * 1');
    echo json_encode($lines);
  });

  $app->get('/:line', function($line) use($app) {
    $data = array();
    $directions = get_line_directions($line);

    foreach ($directions as $direction) {
      $temp = array(
        "name"  => $direction->name,
        "stops" => array()
      );
      $temp["stops"] = get_line_route($line,$direction->dirnumber);
      $data[] = $temp;
    }
    
    echo json_encode($data);
  });
});