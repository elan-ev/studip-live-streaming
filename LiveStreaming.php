<?php

/**
 * LiveStreaming plugin class for Stud.IP
 *
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */
require_once 'lib/locallib.inc.php';

class LiveStreaming extends StudIPPlugin implements StandardPlugin, SystemPlugin
{
    const GETTEXT_DOMAIN = 'LiveStreaming';

    public function __construct()
    {
        global $perm;

        parent::__construct();

        // set up translation domain
        bindtextdomain(static::GETTEXT_DOMAIN, $this->getPluginPath() . '/locale');
        bind_textdomain_codeset(static::GETTEXT_DOMAIN, 'UTF-8');
        
        StudipAutoloader::addClassLookups([
            'LiveStream'        => __DIR__ . '/lib/LiveStream.php',
        ]);

        if ($perm->have_perm('admin')) {
            $item = new Navigation($this->_('LiveStreaming konfigurieren'), PluginEngine::getLink($this, array(), 'admin/admin'));
            if (Navigation::hasItem('/admin/config') && !Navigation::hasItem('/admin/config/livestreaming')) {
                Navigation::addItem('/admin/config/livestreaming', $item);
            }
        }
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
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/teacher'));
            $navigation->addSubNavigation('teacher', new Navigation($this->_('Konfiguration'), PluginEngine::getURL('LiveStreaming/player/teacher')));
            $navigation->addSubNavigation('student', new Navigation($this->_('Studentenansicht'), PluginEngine::getURL('LiveStreaming/player/student')));
        } elseif ($perm->have_studip_perm('tutor', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/teacher'));
            $navigation->addSubNavigation('teacher', new Navigation($this->_('Konfiguration'), PluginEngine::getURL('LiveStreaming/player/teacher')));
            $navigation->addSubNavigation('student', new Navigation($this->_('Studentenansicht'), PluginEngine::getURL('LiveStreaming/player/student')));
        } else {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/student'));
            $navigation->addSubNavigation('student', new Navigation($this->_('Live-Stream'), PluginEngine::getURL('LiveStreaming/player/student')));
        }

        return ['livestreaming' => $navigation];
    }
    
    public function perform($unconsumed_path)
    {
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/css/livestream.css');
        PageLayout::addScript($this->getPluginURL() . '/assets/javascripts/livestream.js');

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

    /**
     * Plugin localization for a single string.
     * This method supports sprintf()-like execution if you pass additional
     * parameters.
     *
     * @param String $string String to translate
     * @return translated string
     */
    public function _($string)
    {
        $result = static::GETTEXT_DOMAIN === null
                ? $string
                : dcgettext(static::GETTEXT_DOMAIN, $string, LC_MESSAGES);
        if ($result === $string) {
            $result = _($string);
        }

        if (func_num_args() > 1) {
            $arguments = array_slice(func_get_args(), 1);
            $result = vsprintf($result, $arguments);
        }

        return $result;
    }


    /**
     * Checks if opencast is loaded, and if course id is passed,
     * returns the series id of the course if opencast has been set for the course
     *
     * @param  string  $cid course ID with default null
     * @return bool | array | string
    */
    function checkOpenCast($cid = null) {
        $opencast_plugin = PluginEngine::getPlugin("OpenCast");
        if ($opencast_plugin) {
            if ($cid) {
                if ($opencast_plugin->isActivated($cid)) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
