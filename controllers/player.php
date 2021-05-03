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
        $mode = LiveStream::find(Context::getId())->mode;

        if ($this->plugin->checkOpenCast(Context::getId()) && $livestream_config['oc_player_url']) {
            $this->select_mode = true;
        }

        $this->mode = $mode;

        if ($mode == MODE_DEFAULT) {
            $this->player_username  = $livestream_config['loginname'];
            $this->player_password  = $livestream_config['password'];
            $this->sender_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['sender_url']);
            $this->player_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['player_url']);
            
            $livestream = LiveStream::find(Context::getId());
            
            $this->countdown_activated = intval($livestream->countdown_activated);
            
            if ($this->countdown_activated == 1) {
                if (strtotime($livestream->countdown_timestamp) > 0) {
                    $this->countdown_manuell = 1;
                    $this->next_livestream = $livestream->countdown_timestamp;
                } else {
                    $this->countdown_manuell = 0;
                }
            }
            
            $this->sem_next_session = Seminar::getInstance(Context::getId())->getNextDate();
        }

        if ($mode == MODE_OPENCAST) {
            $refresh_in_seconds = REFRESH_INTERVALS;
            if ($todays_session = get_course_session_today(Context::getId())) {
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
                    $this->upcoming_termin = $todays_session[PENDING]['termin'];
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
                $livestream->countdown_activated = 0;
                $livestream->countdown_timestamp = 0;
            }
            $livestream->store();

            PageLayout::postSuccess($this->plugin->_('Die LiveStreaming Daten wurden erfolgreich gespeichert.'));
        }
        
        $this->redirect('player/teacher');
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
        
        $mode = LiveStream::find(Context::getId())->mode;
        
        if ($mode != MODE_DEFAULT) {
            PageLayout::postError($this->plugin->_('Mode ist ungültig.'));
        } else {
            $countdown_active = Request::int('countdown_active');
            $next_livestream_date = Request::getDateTime('next_livestream_date', 'd.m.Y', 'next_livestream_time', 'H:i')->format('Y-m-d H:i:s');
            
            if (!$next_livestream_date && Request::int('manuell') == 1) {
                PageLayout::postError($this->plugin->_('Die Termin- oder Zeitangabe fehlt oder ist ungültig.'));
            } else {
            
                // update LiveStream countdown option
                $livestream = LiveStream::find(Context::getId());
                $livestream->countdown_activated = $countdown_active;
                if ($countdown_active == 1 && Request::int('manuell') == 1) {
                    $livestream->countdown_timestamp = $next_livestream_date;
                } else {
                    // delete timestamp if countdown is deactivated or next session date is used
                    $livestream->countdown_timestamp = 0;
                }
                $livestream->store();
            }
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
        $mode = LiveStream::find(Context::getId())->mode;
        $error = false;
        if ($mode != MODE_DEFAULT && $mode != MODE_OPENCAST) {
            $error = true;
        }

        if ($mode == MODE_DEFAULT) {
            $this->show_player = true;
            $this->player_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['player_url']);
            $this->mode = $mode;
            
            // countdown
            $livestream = LiveStream::find(Context::getId());
            $next_date_livestream = Seminar::getInstance(Context::getId())->getNextDate();
            if (intval($livestream->countdown_activated) == 1) {
                if (strtotime($livestream->countdown_timestamp) > 0) {
                    $this->livestream_termin = strtotime($livestream->countdown_timestamp);
                } else {
                    $livestream_datetime = explode(" -", $next_date_livestream)[0];
                    $livestream_datetime = explode(", ", $livestream_datetime)[1];
                    $this->livestream_termin = strtotime($livestream_datetime);
                }
            }
            
            // format date to d.m.Y to identify specific chat for the current stream
            $next_date_formatted = explode(" ", explode(" , ", $next_date_livestream)[1])[0];
            
            $thread = BlubberPosting::findBySQL(
            	"Seminar_id = ? AND user_id = ?", 
            	[Context::getId(), 'Livestream_' . $next_date_formatted]
            );

            if (!$thread) {
                $this->makeLivestreamChat();
            }
            
            $this->thread = BlubberPosting::findBySQL(
            	"Seminar_id = ? AND user_id = ?", 
            	[Context::getId(), 'Livestream_' . $next_date_formatted]
            )[0];
            
            $this->course_id     = Context::getId();
		    $this->single_thread = true;
		    BlubberPosting::$course_hashes = (
		    	$this->thread['user_id'] !== $this->thread['Seminar_id'] ? $this->thread['Seminar_id'] : false
		    );
            
        } else {

            $refresh_in_seconds = REFRESH_INTERVALS;
            if (!$livestream_config['oc_player_url'] ||
                    !$this->plugin->checkOpenCast(Context::getId()) ||
                        !$todays_session = get_course_session_today(Context::getId())) {
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
                $this->upcoming_termin = $todays_session[PENDING]['termin'];
            }
            $this->response->add_header('Refresh', $refresh_in_seconds);
        }

        
        if ($this->show_player == true) {
            PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/css/videoplayer.css');
            PageLayout::addScript($this->plugin->getPluginURL() . '/assets/javascripts/videoplayer.js');
            
            $blubber = new Blubber();
	        PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/autoresize.jquery.min.js");
			PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/blubber.js");
			PageLayout::addScript("{$blubber->getPluginURL()}/assets/javascripts/formdata.js");
        }

        if ($error) {
            PageLayout::postInfo($this->plugin->_("Derzeit ist kein Live-Stream für diese Sitzung verfügbar."));
        }
    }
    
    public function show_livestream_blubber_action($thread_id)
    {
    	$this->thread = new BlubberPosting($thread_id);
        if ($this->thread['context_type'] === "private") {
            if (!in_array($GLOBALS['user']->id, $this->thread->getRelatedUsers())) {
                throw new AccessDeniedException("Kein Zugriff auf diesen Blubb.");
            }
        } elseif ($this->thread['context_type'] === "course") {
            if (!$GLOBALS['perm']->have_studip_perm("user", $this->thread['Seminar_id'])) {
                throw new AccessDeniedException("Kein Zugriff auf diesen Blubb.");
            }
        }
        
        $this->course_id     = Context::getId();
        $this->single_thread = true;
        BlubberPosting::$course_hashes = ($this->thread['user_id'] !== $this->thread['Seminar_id'] ? $this->thread['Seminar_id'] : false);
        
        $this->render_template('player/livestream_blubber');
    }
    
    /**
    * Creates a new blubber thread specifically for the livestream. 
    *
    */
    private function makeLivestreamChat()
    {
        BlubberPosting::$course_hashes = Context::getId();
        
        $date_formatted = explode(" ", explode(" , ", Seminar::getInstance(Context::getId())->getNextDate())[1])[0];
        
        $thread = new BlubberPosting();
        $thread['seminar_id'] = Context::getId();
        $thread['context_type'] = 'course';
        $thread['parent_id'] = 0;
        $thread['user_id'] = 'Livestream_' . $date_formatted;
        $thread['description'] = $this->plugin->_('Schreib was, frag was.');
        $thread->store();
    }
}
