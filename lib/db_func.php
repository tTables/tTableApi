<?php

use RedBeanPHP\R;

function get_stop_name($stop_id) {
    return R::getRow('SELECT * FROM stops WHERE id = :id', [
      ':id' => $stop_id
    ]);
}

function get_line_route($line,$dir_number) {
    return R::getAll("Select routes.*, stops.* From routes, stops Where routes.stopid = stops.id AND routes.line = :line AND routes.dirnuber = :dirno Order By routes.line, routes.dirnuber, routes.id",
        array(
            ":line" => $line,
            ":dirno" => $dir_number
        )
    );
}

function get_line_route_wo_stops_names($line,$dir_number) {
    $out = array();
    $stops = R::getAll("Select routes.stopid From routes, stops Where routes.stopid = stops.id AND routes.line = :line AND routes.dirnuber = :dirno Order By routes.line, routes.dirnuber, routes.id",
        array(
            ":line" => $line,
            ":dirno" => $dir_number
        )
    );
    foreach($stops as $stop) {
        $out[] = $stop["stopid"];
    }
    return $out;
}

function get_line_directions($line) {
    return R::findAll('directions', 'WHERE line = :line ORDER BY dirnumber', array(":line"=>$line));
}

function get_trip_numbers($line,$dir_number,$daytype_id) {
    $ret = array();
    $numbers = R::getAll("SELECT DISTINCT tripnumber FROM departures WHERE line=:line AND dirnumber=:dirno AND daytype=:daytype ORDER BY tripnumber",
        array(
            ":line" => $line,
            ":dirno" => $dir_number,
            ":daytype" => $daytype_id
        )
    );

    foreach($numbers as $number) {
        $ret[] = $number["tripnumber"];
    }
    return $ret;
}

function get_departures_hours($line,$dir_number,$stop_id) {
    $ret = array();
    $hours =  R::getAll("Select Distinct departures.hour From departures Where departures.line = :line And departures.dirnumber = :dir_number And departures.stopid = :stop_id Order By hour",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":stop_id" => $stop_id
        )
    );
    foreach($hours as $hour) {
        $ret[] = $hour["hour"];
    }
    return $ret;
}

function get_departures_for_hour($line,$dir_number,$stop_id,$daytype_id,$hour) {
    return R::getAll("Select Distinct departures.min, departures.signs, departures.tripnumber From departures Where departures.hour = :hour And departures.line = :line And departures.dirnumber = :dir_number And departures.stopid = :stop_id And departures.daytype = :daytype Order By min",
        array(
            ":hour" => $hour,
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":stop_id" => $stop_id,
            ":daytype" => $daytype_id,
        )
    );
}

function get_departures_for_stop($line,$dir_number,$stop_id,$daytype_id) {
    return R::getAll("Select Distinct concat(departures.hour,':',departures.min) as departure, departures.signs, departures.tripnumber From departures Where departures.line = :line And departures.dirnumber = :dir_number And departures.stopid = :stop_id And departures.daytype = :daytype Order By tripnumber",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":stop_id" => $stop_id,
            ":daytype" => $daytype_id,
        )
    );
}

function get_all_departures_for_day_and_trip_no($line,$dir_number,$stop_id,$daytype_id,$trip_no) {
    return R::getAll("Select Distinct CONCAT(departures.hour, ':', departures.min) as departure From departures Where departures.line = :line And departures.dirnumber = :dir_number And departures.stopid = :stop_id And departures.daytype = :daytype And departures.tripnumber = :tripno Order By min",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":stop_id" => $stop_id,
            ":daytype" => $daytype_id,
            ":tripno" => $trip_no
    ));
}

function get_trip($line,$dir_number,$daytype_id,$trip_no) {
    return R::getAll("Select Distinct routes.stopid, departures.hour, departures.min, departures.signs, stops.* From routes Inner Join departures On departures.dirnumber = routes.dirnuber And departures.stopid = routes.stopid And departures.line = routes.line Inner Join stops On stops.id = departures.stopid Where routes.line = :line And routes.dirnuber = :dir_number And departures.daytype = :daytype And departures.tripnumber = :trip_no Order By routes.id",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":daytype" => $daytype_id,
            ":trip_no" => $trip_no
        ));
}

function get_last_stop_in_trip($line,$dir_number,$daytype_id,$trip_no) {
    $stop = R::getAll("Select stops.name1, stops.name2 From routes Inner Join departures On departures.dirnumber = routes.dirnuber And departures.stopid = routes.stopid And departures.line = routes.line Inner Join stops On stops.id = departures.stopid Where routes.line = :line And routes.dirnuber = :dir_number And departures.daytype = :daytype And departures.tripnumber = :trip_no Order By routes.id DESC LIMIT 1",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":daytype" => $daytype_id,
            ":trip_no" => $trip_no
        ));
    return $stop[0];
}

function get_signs($line,$dir_number,$stop_id) {
    $current_signs = array();
    $current_stop_signs = array();
    $signs_line_dir_stop = R::getAll("Select Distinct departures.signs From departures Where departures.line = :line And departures.dirnumber = :dir_number And departures.stopid = :stop_id",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":stop_id" => $stop_id
        )
    );
    $signs_line_dir = R::getAll("Select Distinct signs.sign, signs.description From signs Where signs.line = :line And signs.dirnumber = :dir_number Order By sign",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number
        )
    );
    
    foreach($signs_line_dir_stop as $row) {
        foreach(str_split($row["signs"]) as $sign) {
            if($sign != "") {
                $current_stop_signs[] = $sign;
            }
        }
    }
    
    $current_stop_signs = array_unique($current_stop_signs);
    
    foreach($signs_line_dir as $i=>$sign) {
        if(in_array($sign["sign"], $current_stop_signs)) {
            $current_signs[] = $sign;
        }
    }
    
    return $current_signs;
}

function get_signs_line_dir($line,$dir_number) {
    $signs_line_dir = R::getAll("Select Distinct signs.sign, signs.description From signs Where signs.line = :line And signs.dirnumber = :dir_number Order By sign",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number
        )
    );
    
    return $signs_line_dir;
}

function get_signs_for_trip($line,$dir_number,$trip_no,$daytype) {
    $signs_line_dir = R::getAll("Select Distinct departures.signs From departures Where departures.line = :line And departures.dirnumber = :dir_no AND departures.tripnumber = :trip_number AND departures.daytype = :daytype",
        array(
            ":line" => $line,
            ":dir_number" => $dir_number,
            ":trip_number" => $trip_no,
            ":daytype" => $daytype
        )
    );
    
    return $signs_line_dir;
}

function get_lines_from_stop($stop_id) {
    return R::getAll("Select Distinct Group_Concat(Distinct departures.line Order By departures.line*1 Separator ' ') As `lines` From departures Where departures.stopid = " . $stop_id);
}

function get_lines_and_directions_no_from_stop($stop_id) {
    return R::getAll("Select Distinct departures.line, departures.dirnumber From departures Where departures.stopid = :stopid Order By departures.line * 1, departures.dirnumber", array(
        ":stopid" => $stop_id
    ));
}

function get_stops_filter($filter = '') {
    $filter = $filter == '' ? null : '%' . $filter . '%';
    if($filter != null) {
        $stops =  R::findAll('stops', ' name1 LIKE :filter OR name2 LIKE :filter ORDER BY name1,name2',array(
            ":filter" => $filter
        ));
    } else {
        //$stops =  R::findAll('stops', 'ORDER BY name1,name2');
		$stops = R::getAll("SELECT *, stops.id as idd, (Select Distinct Group_Concat(Distinct departures.line Order By departures.line*1 Separator ' ') As `lines` From departures Where departures.stopid = idd) AS 'lines' FROM stops ORDER BY name1,name2");
    }
    return $stops;
}

function get_stop_chrono_departures($stop_id,$daytype_id) {
    return R::getAll("Select departures.line, departures.hour, departures.min, departures.tripnumber, departures.dirnumber, directions.name as directionname From departures Inner Join directions On directions.dirnumber = departures.dirnumber And directions.line = departures.line Where departures.stopid = :stop_id And departures.daytype = :daytype_id Order By departures.hour, departures.min",array(
        ":stop_id" => $stop_id,
	":daytype_id" => $daytype_id
    ));
}

function get_direction_name($line, $direction_number) {
    return R::findOne("directions", " line = :line AND dirnumber = :dir_no",
        array(
            ":line" => $line,
            ":dir_no" => $direction_number
        ))->name;
}