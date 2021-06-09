<?php

/**
 * Stream Player controller class for Stud.IP
 *
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 **/

class PlayerController extends PluginController {
    
    private $allow_player_before_start = 5*60; // 5 minutes before session start, show the player and chat!
    /**
    * Displays the player and access data for the stream for lecturers.
    */
    public function teacher_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException($this->plugin->_('Sie verfügen nicht über die notwendigen Rechte für diese Aktion'));
        }
        
        Navigation::activateItem('/course/livestreaming/teacher');
        
        $livestream_config = LiveStream::getConfig();
        $livestream = LiveStream::find(Context::getId());
        
        if (!$livestream) {
            $livestream = new LiveStream();
            $livestream->seminar_id = Context::getId();
            $livestream->mode = MODE_DEFAULT;
            $livestream->store();
        }

        $mode = $livestream->mode;

        if ($this->plugin->checkOpenCast(Context::getId()) && $livestream_config['oc_player_url']) {
            $this->select_mode = true;
        } else if ($mode == MODE_OPENCAST) { // forcing the mode to DEFAULT when the opencast is not activated/configured properly
            $mode = MODE_DEFAULT;
            $livestream->mode = MODE_DEFAULT;
            $livestream->store();
        }

        $this->mode = $mode;

        if ($mode == MODE_DEFAULT) {
            $this->player_username  = $livestream_config['loginname'];
            $this->player_password  = $livestream_config['password'];
            $this->sender_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['sender_url']);
            $this->player_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['player_url']);

            $this->countdown_activated = intval($livestream->countdown_activated);
            
            $livechat = 0;
            $terminate_session = 0;
            $options = json_decode($livestream->options);
            if ($options) {
                if ($options->livechat) {
                    $livechat = intval($options->livechat->active);
                }
                if ($options->termin && $options->termin->terminate_session) {
                    $terminate_session = intval($options->termin->terminate_session);
                }
            }
            $this->chat_active = $livechat;
            $this->terminate_session = $terminate_session;
            if ($this->countdown_activated == 1) {
                if (intval($livestream->session_start) > 0) {
                    $this->countdown_manuell = 1;
                    $this->next_livestream = intval($livestream->session_start);
                    $this->next_livestream_end = intval($livestream->session_end);
                    if ($this->next_livestream_end < strtotime('now')) {
                        PageLayout::postWarning($this->plugin->_('Die Countdown-Zeit ist abgelaufen. Bitte versuchen Sie, den Termin zu erneuern.'));
                    } else if ($this->next_livestream_end > strtotime('now') && $this->next_livestream < strtotime('now')) {
                        PageLayout::postInfo($this->plugin->_('Live-Streaming läuft derzeit.'));
                    }
                } else {
                    $this->countdown_manuell = 0;
                }
            }

            $this->sem_next_session = Seminar::getInstance(Context::getId())->getNextDate();
        }

        if ($mode == MODE_OPENCAST) {
            $refresh_in_seconds = REFRESH_INTERVALS;
            if ($todays_session = get_course_session_from_today(Context::getId())) {
                if (isset($todays_session[LIVE])) {
                    $this->show_live_countdown = true;
                    $refresh_in_seconds = $todays_session[LIVE]['refresh_seconds'];
                    $this->live_termin = $todays_session[LIVE]['termin'];
                }
    
                if (isset($todays_session[PENDING])) {
                    if (!isset($todays_session[LIVE])) {
                        $refresh_in_seconds = $todays_session[PENDING]['refresh_seconds'];
                    }
                    $this->show_countdown = true;
                    $this->upcoming_termin = $todays_session[PENDING]['termin']->date;
                }
                $this->response->add_header('Refresh', $refresh_in_seconds);
            } else {
                $this->info_message = MessageBox::info($this->plugin->_("Derzeit ist kein Live-Stream für diese Sitzung verfügbar."));
            }
        }

    }

    public function select_mode_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException($this->plugin->_('Sie verfügen nicht über die notwendigen Rechte für diese Aktion'));
        }
        CSRFProtection::verifyUnsafeRequest();
        $mode  = Request::get('livestream-mode');

        $error = false;
        if ($mode != MODE_DEFAULT && $mode != MODE_OPENCAST) {
            PageLayout::postError($this->plugin->_('Mode ist ungültig.'));
            $error = true;
        }

        if (!$error) {
            if ($livestream = LiveStream::find(Context::getId())) {
                $livestream->mode = $mode;
            } else {
                $livestream = new LiveStream();
                $livestream->seminar_id = Context::getId();
                $livestream->mode = $mode;
            }
            $livestream->store();

            PageLayout::postSuccess($this->plugin->_('Die LiveStreaming Daten wurden erfolgreich gespeichert.'));
        }
        
        $this->redirect('player/teacher');
    }

    /**
    * Displays the player for students.
    */
    public function student_action()
    {
        Navigation::activateItem('/course/livestreaming/student');
        
        $this->title = $this->plugin->_('LiveStreaming');

        $livestream_config = LiveStream::getConfig();
        $livestream = LiveStream::find(Context::getId());
        $mode = $livestream->mode;
        $error = false;
        if ($mode != MODE_DEFAULT && $mode != MODE_OPENCAST) {
            $error = true;
        }

        if ($mode == MODE_DEFAULT) {
            $this->show_player = true;
            $this->player_url = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['player_url']);
            $this->mode = $mode;
            
            // countdown
            $next_date_livestream = Seminar::getInstance(Context::getId())->getNextDate();
            if (intval($livestream->countdown_activated) == 1) {
                if ($livestream->session_start > 0) {
                    $this->upcoming_termin = $livestream->session_start;
                } else {
                    $livestream_datetime = explode(" -", $next_date_livestream)[0];
                    $livestream_datetime = explode(", ", $livestream_datetime)[1];
                    $this->upcoming_termin = strtotime($livestream_datetime);
                }

                if ($this->upcoming_termin < strtotime('now')) {
                    $this->upcoming_termin = 0;
                }

                $this->show_countdown = $this->upcoming_termin ? true : false;

                // is a session currently in progress info for displaying the stream and chat
                $session_info = $this->CheckSessionProgress($livestream);
                $this->show_player = $session_info->can_show_player;
                if ($session_info->refresh_in_seconds > 0) {
                    $this->response->add_header('Refresh', $session_info->refresh_in_seconds);
                }
            }

            // only load chat if it is activated in teacher settings
            $options = json_decode($livestream->options);
            $this->chat_active = false;
            
            if ($options->livechat->active) {
                $this->chat_active = true;
                // format date to d.m.Y to identify specific chat for the current stream
                $next_date_formatted = explode(" ", explode(", ", $next_date_livestream)[1])[0];
                
                if (StudipVersion::olderThan('4.5')) {
                    $this->thread           = $this->getBlubberThreadOldStudip($next_date_formatted);
                    $this->course_id        = Context::getId();
	                $this->single_thread    = true;
	                BlubberPosting::$course_hashes = (
	                	$this->thread['user_id'] !== $this->thread['Seminar_id'] ? $this->thread['Seminar_id'] : false
	                );
                } else {
                    $this->thread = $this->getBlubberThread($next_date_formatted);
                }
            }
            
        } else {

            $refresh_in_seconds = REFRESH_INTERVALS;
            if (!$livestream_config['oc_player_url'] ||
                    !$this->plugin->checkOpenCast(Context::getId()) ||
                        !$todays_session = get_course_session_from_today(Context::getId())) {
                $error = true;
            }

            if (isset($todays_session[LIVE])) {
                $this->show_player = true;
                $refresh_in_seconds = $todays_session[LIVE]['refresh_seconds'];
                $this->termin = $todays_session[LIVE]['termin'];
                $this->player_url= str_replace(URLPLACEHOLDER, $todays_session[LIVE]['capture_agent'], $livestream_config['oc_player_url']);
            }

            if (isset($todays_session[PENDING])) {
                if (!isset($todays_session[LIVE])) {
                    $refresh_in_seconds = $todays_session[PENDING]['refresh_seconds'];
                }
                $this->show_countdown = true;
                $this->upcoming_termin = $todays_session[PENDING]['termin']->date;
            }
            $this->response->add_header('Refresh', $refresh_in_seconds);
        }

        
        if ($this->show_player == true) {
            PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/css/videoplayer.css');
            PageLayout::addScript($this->plugin->getPluginURL() . '/assets/javascripts/videoplayer.js');
            
            if (StudipVersion::olderThan('4.5')) {
                $blubber = new Blubber();
                PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/autoresize.jquery.min.js");
                PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/blubber.js");
                PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/formdata.js");
            }
        }

        if ($error || !$this->show_player && !$this->show_countdown) {
            PageLayout::postInfo($this->plugin->_("Derzeit ist kein Live-Stream für diese Sitzung verfügbar."));
        }
    }

    /**
    * Toggles the countdown for normal live streaming.
    */
    public function toggle_countdown_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException($this->plugin->_('Sie verfügen nicht über die notwendigen Rechte für diese Aktion'));
        }
        CSRFProtection::verifyUnsafeRequest();
        
        $livestream = LiveStream::find(Context::getId());
        $mode = $livestream->mode;
        
        if ($mode != MODE_DEFAULT) {
            PageLayout::postError($this->plugin->_('Mode ist ungültig.'));
        } else {
            $countdown_active = Request::get('countdown_active') ? 1 : 0;
            $manuell = Request::int('manuell');
            $terminate_session = Request::get('terminate_session') ? 1 : 0;

            if ($countdown_active) {
                $livestream->countdown_activated = 1;
                if ($manuell) {
                    $next_livestream_startdate = $this->getDateTime('next_livestream_date', 'd.m.Y', 'next_livestream_starttime', 'H:i');
                    $next_livestream_enddate = $this->getDateTime('next_livestream_date', 'd.m.Y', 'next_livestream_endtime', 'H:i');
                    $next_livestream_startdate = $next_livestream_startdate ? $next_livestream_startdate->format('Y-m-d H:i:s') : false;
                    $next_livestream_enddate = $next_livestream_enddate ? $next_livestream_enddate->format('Y-m-d H:i:s') : false;
                    if (!$next_livestream_startdate || !$next_livestream_enddate || strtotime($next_livestream_startdate) >= strtotime($next_livestream_enddate)) {
                        PageLayout::postError($this->plugin->_('Die Termin- oder Zeitangabe fehlt oder ist ungültig.'));
                        $livestream->countdown_activated = 0;
                        $livestream->session_start = 0;
                        $livestream->session_end = 0;
                    } else {
                        $livestream->session_start = strtotime($next_livestream_startdate);
                        $livestream->session_end = strtotime($next_livestream_enddate);
                    }
                } else {
                    $livestream->session_start = 0;
                    $livestream->session_end = 0;
                }
            } else {
                $livestream->countdown_activated = 0;
                $livestream->session_start = 0;
                $livestream->session_end = 0;
                $terminate_session = 0;
            }

            // terminate_session
            $options = json_decode($livestream->options) ? json_decode($livestream->options) : new \stdClass();
            $options->termin->terminate_session = $terminate_session;
            $livestream->options = json_encode($options);

            $livestream->store();
        }
        $this->redirect('player/teacher');
    }
    
    /**
    * Toggles the live chat for normal live streaming.
    */
    public function toggle_chat_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException($this->plugin->_('Sie verfügen nicht über die notwendigen Rechte für diese Aktion'));
        }
        CSRFProtection::verifyUnsafeRequest();
        
        $livestream = LiveStream::find(Context::getId());
        $mode = $livestream->mode;
        
        if ($mode != MODE_DEFAULT) {
            PageLayout::postError($this->plugin->_('Mode ist ungültig.'));
        } else {
            $chat_active = Request::get('chat_active') ? 1 : 0;
            $options = json_decode($livestream->options) ? json_decode($livestream->options) : new \stdClass();
            $options->livechat->active = $chat_active;
            $livestream->options = json_encode($options);
            
            $livestream->store();
        }
        
        $this->redirect('player/teacher');
    }

    /**
    * getDateTime function duplication - in order to cover all version of Stud.IP 
    */
    private function getDateTime(
        $date_param = 'date',
        $date_format = 'Y-m-d',
        $time_param = 'time',
        $time_format = 'H:i',
        $default = null) 
    {
        $date_value = Request::get($date_param);
        $time_value = Request::get($time_param);

        if (!$date_value or !$time_value) {
            //In case one of the two fields is not set
            //use the default value, if any:
            if ($default instanceof DateTime) {
                return $default;
            } else {
                return new DateTime();
            }
        }

        //A date and time value could be retrieved.
        //Now we must process it according to the format
        //specifications.

        $value = new DateTime();
        //The time zone may not be set in the fields
        //so we use the default timezone.
        $time_zone = $value->getTimezone();

        return DateTime::createFromFormat(
            $date_format . ' ' . $time_format,
            $date_value . ' ' . $time_value,
            $time_zone
        );
    }
    
    /**
    * Finds the blubber thread corresponding to the specific livestream
    * identified by date. Creates a blubber thread if it does not exist.
    *
    * @param string $formatted_date a date in the format d.m.Y
    *
    * @return BlubberThread
    */
    private function getBlubberThread($formatted_date)
    {
        $threads = BlubberThread::findBySeminar(Context::getId());
        $thread_exists = false;
        $thread_id = null;
        
        $return_thread = null;
        
        $livestream_user_id = 'Livestream_' . $formatted_date;

        foreach ($threads as $thread) {
            if ($thread->user_id == $livestream_user_id) {
                $thread_exists = true;
                $thread_id = $thread->thread_id;
                $return_thread = BlubberThread::find($thread_id);
            }
        }

        if (!$thread_exists) {
            
            $thread = BlubberThread::create([
                'context_type'      => 'course',
                'context_id'        => Context::getId(),
                'user_id'           => $livestream_user_id,
                'external_contact'  => 0,
                'display_class'     => null,
                'visible_in_stream' => 1,
                'commentable'       => 1,
                'content'           => ''
            ]);
            
            $return_thread = $thread;
        }
        
        return $return_thread;
        
    }
    
    /**
    * Finds the blubber thread corresponding to the specific livestream
    * identified by date for studip versions older than 4.5. 
    * Creates a blubber thread if it does not exist.
    *
    * @param string $formatted_date a date in the format d.m.Y
    *
    * @return BlubberPosting
    */
    private function getBlubberThreadOldStudip($formatted_date)
    {
        $return_thread = null;
    
        $thread = BlubberPosting::findBySQL(
        	"Seminar_id = ? AND user_id = ?", 
        	[Context::getId(), 'Livestream_' . $formatted_date]
        );

        if (!$thread) {
            BlubberPosting::$course_hashes = Context::getId();
            
            $thread = new BlubberPosting();
            $thread['seminar_id'] = Context::getId();
            $thread['context_type'] = 'course';
            $thread['parent_id'] = 0;
            $thread['user_id'] = 'Livestream_' . $formatted_date;
            $thread['description'] = $this->plugin->_('Schreib was, frag was.');
            $thread->store();
        }
        
        $return_thread = BlubberPosting::findBySQL(
        	"Seminar_id = ? AND user_id = ?", 
        	[Context::getId(), 'Livestream_' . $formatted_date]
        )[0];
	    
	    return $return_thread;
    }
    
    /**
    * Checks if the session is in progress, and returns the info of the session
    *
    * @param object $livestream the livestream object
    *
    * @return stdClass
    */
    private function CheckSessionProgress($livestream)
    {
        $session_info = new \stdClass();
        $today_timestamp = strtotime('now');
        $options = json_decode($livestream->options);
        // Check if coundown is selected!
        if (intval($livestream->countdown_activated) == 1) {
            // Check if the manual appointment is set.
            if (intval($livestream->session_start) > 0) {
                $session_info->can_show_player = intval($livestream->session_start) - ($this->allow_player_before_start) <= $today_timestamp && $today_timestamp <= intval($livestream->session_end);
                if (intval($livestream->session_start) > $today_timestamp + $this->allow_player_before_start) {
                    // Refresh the session when the time reaches the (5 min) before
                    $session_info->refresh_in_seconds = intval($livestream->session_start) - $today_timestamp - $this->allow_player_before_start;
                } else {
                    // Refresh the page after the session ends
                    //intval($options->termin->terminate_session)
                    $session_info->refresh_in_seconds = intval($options->termin->terminate_session) ? intval($livestream->session_end) - $today_timestamp : 0;
                }
            } else {
                // Check if the automatic next appointment is selected!
                // First look for the actual termin.
                $where = "range_id = ? AND date <= ? AND end_time >= ?";
                $session_date = \CourseDate::findOneBySQL($where, 
                        [Context::getId(),
                            $today_timestamp + $this->allow_player_before_start,
                            $today_timestamp,
                        ]);
                // Jf there is no actual termin, then look for future termin
                if (!$session_date) {
                    $where = "range_id = ? AND date > ?";
                    $session_date = \CourseDate::findOneBySQL($where, 
                        [Context::getId(),
                            $today_timestamp + $this->allow_player_before_start
                        ]);
                }    

                if ($session_date) {
                    $session_info->can_show_player = intval($session_date->date) - ($this->allow_player_before_start) <= $today_timestamp && $today_timestamp <= intval($session_date->end_time);
                    if (intval($session_date->date) > $today_timestamp + $this->allow_player_before_start) {
                        // Refresh the session when the time reaches the (5 min) before
                        $session_info->refresh_in_seconds = intval($session_date->date) - $today_timestamp - $this->allow_player_before_start;
                    } else {
                        // Refresh the page after the session ends
                        $session_info->refresh_in_seconds = intval($options->termin->terminate_session) ? intval($session_date->end_time) - $today_timestamp : 0;
                    }
                }
            }
        }
        return $session_info;
    }
}
