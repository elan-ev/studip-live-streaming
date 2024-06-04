<?php
/**
 * LiveStreamLib - LiveStreaming Library Class
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class LiveStreamLib {

    public const URLPLACEHOLDER = '<id>';
    public const MODE_OPENCAST = 'opencast';
    public const MODE_DEFAULT = 'default';
    public const PENDING = 'before';
    public const LIVE = 'current';
    public const REFRESH_INTERVALS = 600; //EACH 10 mins TODO:Add to config (dynamic)

    /**
     * Gets the scheduled session of today matching with opencast records.
     *
     * @param string $course_id course id
     *
     * @return array the session info.
     */
    public static function getOCScheduledSession($course_id) {

        $today_timestamp = strtotime('today midnight');
        $where = "range_id = ? and date >= ? ORDER BY date ASC";
        $session_coursedates = CourseDate::findBySQL($where, [$course_id, $today_timestamp]);

        if (!$session_coursedates) {
            return [false, false];
        } 

        $todays_scheduled_sessions = [];

        $now_timestamp = strtotime('now');

        $closest_refresh_in_seconds = 0;

        foreach ($session_coursedates as $session_coursedate) {
            if (!$capture_agent = self::GetOCCaptureAgent($session_coursedate->termin_id, $course_id)) {
                continue;
            }

            $start_session_timestamp =  intval($session_coursedate->date);

            $end_session_timestamp =  intval($session_coursedate->end_time);

            $schedule_data['termin'] = $session_coursedate;
            $schedule_data['room_name'] = $session_coursedate->room_booking->resource->name;
            $schedule_data['capture_agent'] = $capture_agent;

            // Upcoming termins. We only consider the closest upcoming termin.
            if (!isset($todays_scheduled_sessions[self::PENDING]) && $now_timestamp < $start_session_timestamp) {
                $todays_scheduled_sessions[self::PENDING] = $schedule_data;
                $refresh_seconds = $start_session_timestamp - $now_timestamp;
                if (empty($closest_refresh_in_seconds) || $refresh_seconds < $closest_refresh_in_seconds) {
                    $closest_refresh_in_seconds = $refresh_seconds;
                }
            }

            // Live termins.
            if ($start_session_timestamp <= $now_timestamp && $now_timestamp < $end_session_timestamp) {
                $todays_scheduled_sessions[self::LIVE][$session_coursedate->termin_id] = $schedule_data;
                $refresh_seconds = $end_session_timestamp - $now_timestamp;
                if (empty($closest_refresh_in_seconds) || $refresh_seconds < $closest_refresh_in_seconds) {
                    $closest_refresh_in_seconds = $refresh_seconds;
                }
            }
        }

        if (empty($closest_refresh_in_seconds)) {
            $closest_refresh_in_seconds = self::REFRESH_INTERVALS;
        }
        return [$todays_scheduled_sessions, $closest_refresh_in_seconds];
    }


    /**
     * Returns the Capture Agent for the current session extracted from DB records of Opencast Plugin.
     *
     * @param string $termin_id the id of the termin
     * @param string $cid course id
     *
     * @return ?string the capture agent or false if no capture agent is available.
     * @throws Exception mostly PDOExceptions.
     */
    private static function GetOCCaptureAgent($termin_id, $cid) {
        try {
            $date = new SingleDate($termin_id);

            // Check resources.
            $oc_resource = DBManager::get()->fetchOne(
                'SELECT * FROM `oc_resources` WHERE `resource_id` = :resource_id',
                [':resource_id' => $date->resource_id]
            );
            // If resource does not exist, we return false.
            if (empty($oc_resource) && empty($oc_resource['capture_agent'])) {
                return false;
            }
            $capture_agent = $oc_resource['capture_agent'];

            // Get the scheduled recordings for that capture agent from oc_scheduled_recordings.
            $oc_scheduled_recording = DBManager::get()->fetchOne(
                'SELECT * FROM oc_scheduled_recordings 
                    WHERE seminar_id = :cid AND date_id = :date_id AND capture_agent = :capture_agent AND status = :status',
                [':cid' => $cid, 'date_id' => $termin_id, 'capture_agent' => $capture_agent, 'status' => 'scheduled']);

            // If there is no scheduled recording record for that termin, we return false.
            if (empty($oc_scheduled_recording) && empty($oc_scheduled_recording['capture_agent'])) {
                return false;
            }
            return $oc_scheduled_recording['capture_agent'];
        } catch (Throwable $th) {
            throw new Exception('Geplante Aufzeichnung konnte nicht gefunden werden: ' . $th->getMessage());
        }
    }
}
