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
require_once __DIR__ . '/lib/locallib.inc.php';

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
        
        // set up blubber updating so new postings are automatically loaded
        if (StudipVersion::olderThan('4.5') && UpdateInformation::isCollecting()) {
            $data = Request::getArray("page_info");
            if (isset($data['Blubber'])) {
                $output = array();
                $stream = BlubberStream::getCourseStream($data['Blubber']['context_id']);
            
                $last_check = $data['server_timestamp'] ?: (time() - 5 * 60);

                $new_postings = $stream->fetchNewPostings($last_check, time());

                $blubber = new Blubber();
                $factory = new Flexi_TemplateFactory($blubber->getPluginPath()."/views");
                foreach ($new_postings as $new_posting) {
                    if ($new_posting['root_id'] === $new_posting['topic_id']) {
                        $thread = $new_posting;
                        $template = $factory->open("streams/thread.php");
                        $template->set_attribute('thread', $new_posting);
                    } else {
                        $thread = new BlubberPosting($new_posting['root_id']);
                        $template = $factory->open("streams/comment.php");
                        $template->set_attribute('posting', $new_posting);
                    }
                    BlubberPosting::$course_hashes = ($thread['user_id'] !== $thread['Seminar_id'] ? $thread['Seminar_id'] : false);
                    $template->set_attribute("course_id", $data['Blubber']['seminar_id']);
                    $output['postings'][] = array(
                        'posting_id' => $new_posting['topic_id'],
                        'discussion_time' => $new_posting['discussion_time'],
                        'mkdate' => $new_posting['mkdate'],
                        'root_id' => $new_posting['root_id'],
                        'content' => $template->render()
                    );
                }
                UpdateInformation::setInformation("Blubber.getNewPosts", $output);
                UpdateInformation::setInformation("Blubber.handleScrollForLiveStream", count($output['postings']));
                //Events-Queue:
                $db = DBManager::get();
                $events = $db->query(
                    "SELECT event_type, item_id " .
                    "FROM blubber_events_queue " .
                    "WHERE mkdate >= ".$db->quote($last_check)." " .
                    "ORDER BY mkdate ASC " .
                "")->fetchAll(PDO::FETCH_ASSOC);
                UpdateInformation::setInformation("Blubber.blubberEvents", $events);
                $db->exec(
                    "DELETE FROM blubber_events_queue " .
                    "WHERE mkdate < UNIX_TIMESTAMP() - 60 * 60 * 6 " .
                "");
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
    * Returns the tab navigation for the LiveStreaming plugin in the given course.
    *
    * @param $course_id the given course ID
    */
    public function getTabNavigation($course_id) 
    {
        global $perm;

        if ($perm->have_studip_perm('admin', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/teacher'));
            $navigation->addSubNavigation('teacher', 
                new Navigation($this->_('Konfiguration'), PluginEngine::getURL('LiveStreaming/player/teacher')));
            $navigation->addSubNavigation('student', 
                new Navigation($this->_('Studentenansicht'), PluginEngine::getURL('LiveStreaming/player/student')));
        } elseif ($perm->have_studip_perm('tutor', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/teacher'));
            $navigation->addSubNavigation('teacher', 
                new Navigation($this->_('Konfiguration'), PluginEngine::getURL('LiveStreaming/player/teacher')));
            $navigation->addSubNavigation('student', 
                new Navigation($this->_('Studentenansicht'), PluginEngine::getURL('LiveStreaming/player/student')));
        } else {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('LiveStreaming/player/student'));
            $navigation->addSubNavigation('student', 
                new Navigation($this->_('Live-Stream'), PluginEngine::getURL('LiveStreaming/player/student')));
        }

        return ['livestreaming' => $navigation];
    }
    
    public function perform($unconsumed_path)
    {
        if (StudipVersion::olderThan('4.5')) {
            $blubber = new Blubber();
            $blubber->addStylesheet('assets/stylesheets/blubber.less');
        }
    
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
        if (!$this->isActivated($course_id)) {
            return;
        }

        $perm = $GLOBALS['perm'];

        $landing = 'player/student';
        if ($perm->have_studip_perm('tutor', $course_id)) {
            $landing = 'player/teacher';
        }

        $navigation = new Navigation(
            'livestreaming',
            PluginEngine::getURL($this, [], $landing)
        );
        
        $navigation->setImage(
            Icon::create('video2',
                    Icon::ROLE_INACTIVE,
                    ['title' => 'LiveStreaming']
                ));
        return $navigation;
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
