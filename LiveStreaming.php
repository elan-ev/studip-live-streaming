<?php

/**
 * StreamPlayer plugin class for Stud.IP
 *
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class LiveStreaming extends StudIPPlugin implements StandardPlugin
{
    public function __construct()
    {
        global $perm;

        parent::__construct();

        // set up translation domain
        bindtextdomain('LiveStreaming', dirname(__FILE__) . '/locale');

    }

    /**
    * Returns the plugin name
    */
    public function getPluginName()
    {
        return 'LiveStreaming';
    }

    /**
    * Returns the tab navigation for the OSKA plugin in the given course.
    *
    * @param $course_id the given course ID
    */
    public function getTabNavigation($course_id) 
    {
        global $perm;

        if ($perm->have_studip_perm('admin', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/admin'));
            $navigation->addSubNavigation('admin', new Navigation(_('Übersicht'), PluginEngine::getURL('LiveStreaming/player/admin')));
        } elseif ($perm->have_studip_perm('tutor', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/teacher'));
            $navigation->addSubNavigation('teacher', new Navigation(_('Übersicht'), PluginEngine::getURL('LiveStreaming/player/teacher')));
        } else {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/student'));
            $navigation->addSubNavigation('student', new Navigation(_('Übersicht'), PluginEngine::getURL('LiveStreaming/player/student')));
        }

        return ['livestreaming' => $navigation];
    }
    
    public function perform($unconsumed_path)
    {
        PageLayout::addStylesheet($this->getPluginURL() . '/css/streamplayer.css');
        PageLayout::addStylesheet($this->getPluginURL() . '/node_modules/video.js/dist/video-js.min.css');
        PageLayout::addScript($this->getPluginURL() . '/node_modules/video.js/dist/video.js');

        parent::perform($unconsumed_path);
    }

    /**
    * Returns the course summary page template.
    *
    * @param $course_id the given course ID
    */
    public function getInfoTemplate($course_id)
    {
        return NULL;
    }

    /**
    * Returns the icon navigation object.
    *
    * @param $course_id the given course ID
    * @param $last_visit point in time of the user's last visit
    * @param $user_id the ID of the given user
    */
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        return NULL;
    }

}
