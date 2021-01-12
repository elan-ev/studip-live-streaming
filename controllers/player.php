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
        $mode = LiveStream::find(Context::getId())->mode;
        $error = false;
        if ($mode != MODE_DEFAULT && $mode != MODE_OPENCAST) {
            $error = true;
        }

        if ($mode == MODE_DEFAULT) {
            $this->show_player = true;
            $this->player_url       = str_replace(URLPLACEHOLDER, Context::getId(), $livestream_config['player_url']);
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
        }

        if ($error) {
            PageLayout::postInfo($this->plugin->_("Derzeit ist kein Live-Stream für diese Sitzung verfügbar."));
        }
    }
}
