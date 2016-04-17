<?php

use RedBeanPHP\R;

/* 
 * Copyright 2015 Patryk Wychowaniec.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

function get_departures($line,$direction_number,$stop_id) {
    $data = array(
        "line" => $line,
        "dir_no" => $direction_number,
        "route" => get_line_route($line, $direction_number),
        "signs" => get_signs($line, $direction_number, $stop_id),
        "direction_name" => get_direction_name($line, $direction_number),
        "hours" => array(),
        "line_date" => "",
        "other_lines" => array(),
        "departures" => array()
    );
    $daytypes = R::find("daytypes");
    $hours = get_departures_hours($line, $direction_number, $stop_id);
    $data["hours"] = $hours;
    $data["other_lines"] = R::getAll("Select Distinct departures.dirnumber, directions.name, directions.line From departures Inner Join directions On directions.dirnumber = departures.dirnumber And directions.line = departures.line Where departures.stopid = :stopid Order By directions.line*1, departures.dirnumber",
        array(
            ":stopid" => $stop_id
        ));
    $data["line_date"] = R::findOne("lines", " line = :line", array("line" => $line))->date;

    foreach($daytypes as $daytype) {
        $temp = array(
            "daytype" => $daytype->name,
            "daytype_number" => $daytype->id,
            "count" => 0,
            "departures" => array()
        );
        foreach($hours as $hour) {
            $departures = get_departures_for_hour($line, $direction_number, $stop_id, $daytype->id, $hour);
            if(count($departures)) {
                $temp["count"]++;
            }
            $temp["departures"][] = array(
                "hour" => $hour,
                "minutes" => $departures
            );
        }
        $data["departures"][] = $temp;
    }
    return $data;
}

function get_list_departures($line,$direction_number) {
    $st = microtime(true);
    $daytypes = R::find("daytypes");
    $data = array(
        "line" => $line,
        "dir_no" => $direction_number,
        "route" => get_line_route($line, $direction_number),
        "signs" => get_signs_line_dir($line,$direction_number),
        "daytypes" => $daytypes,
        "direction_name" => get_direction_name($line,$direction_number),
        "departures" => array(),
        "numbers" => array(),
        "counts" => array()
    );

    foreach($daytypes as $daytype) {
        $numbers = get_trip_numbers($line,$direction_number,$daytype->id);
        $data["numbers"][] = $numbers;
        $tmp = array();

        foreach($data["route"] as $route_row) {
            $tmp[] = get_departures_for_stop($line,$direction_number,$route_row["stopid"],$daytype->id);
        }
        $count=0;
        foreach ($tmp as $tmp_row) {
            $count += count($tmp_row);
        }

        $data["departures"][] = $tmp;
        $data["counts"][] = $count;
    }
    $end = microtime(true);

    // echo ($end-$st);

    return $data;
}