<?php
/**
 * locallib.inc.php - LiveStreaming plugin class for Stud.IP
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */
define('URLPLACEHOLDER', '<id>');
define('MODE_OPENCAST', 'opencast');
define('MODE_DEFAULT', 'default');
define('PENDING', 'before');
define('LIVE', 'current');
define('REFRESH_INTERVALS', 600); //EACH 10 mins TODO:Add to config (dynamic)

function get_course_session_from_today($course_id) {
    
    $today_timestamp = strtotime('today midnight');
    $where = "range_id = ? and date >= ? ORDER BY date ASC";
    $session_coursedates = \CourseDate::findBySQL($where, [$course_id, $today_timestamp]);

    if (!$session_coursedates) {
        return false;
    } 

    $todays_scheduled_sessions = [];

    $now_timestamp = strtotime('now');

    foreach ($session_coursedates as $session_coursedate) {


        if (!$capture_agent = GetOCCaptureAgent($session_coursedate->termin_id)) {
            continue;
        }
        
        $start_session_timestamp =  $session_coursedate->date;
    
        $end_session_timestamp =  $session_coursedate->end_time;

        $status = '';
        $schedule_data['termin'] = $session_coursedate;
        $schedule_data['capture_agent'] = $capture_agent;
        if (!isset($todays_scheduled_sessions[PENDING]) && $now_timestamp < $start_session_timestamp) // Upcoming
        {
            $schedule_data['refresh_seconds'] = $start_session_timestamp - $now_timestamp;
            $todays_scheduled_sessions[PENDING] = $schedule_data;
        } 
        else if (!isset($todays_scheduled_sessions[LIVE]) && $start_session_timestamp <= $now_timestamp && $now_timestamp < $end_session_timestamp) //live
        {
            $schedule_data['refresh_seconds'] = $end_session_timestamp - $now_timestamp;
            $todays_scheduled_sessions[LIVE] = $schedule_data;
        }
    }
    return $todays_scheduled_sessions;
}



function GetOCCaptureAgent($termin_id) {
    $date = new SingleDate($termin_id);
    $oc_resource = Opencast\Models\OCResources::findOneBySQL('resource_id = ?',[$date->resource_id]);
    if (!$oc_resource && !$oc_resource->capture_agent) {
        return false;
    }
    return $oc_resource->capture_agent;
}

?>
