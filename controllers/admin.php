<?php
/**
 * LiveStreaming Admin controller class for Stud.IP
 *
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 **/

class AdminController extends PluginController {

    /**
     * Constructs the controller.
     *
     */
    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);

        $this->plugin = $dispatcher->current_plugin;
    }

    /**
     * {@inheritdoc}
     */
    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        // Permission check
        if ($GLOBALS['user']->perms !== 'root') {
            throw new AccessDeniedException();
        }

        Navigation::activateItem('/admin/config/livestreaming');

    }

    public function admin_action()
    {
        PageLayout::setTitle($this->plugin->_('LiveStreaming Daten'));

        $livestream_config = LiveStream::getConfig();

        $this->player_url           = $livestream_config['player_url'];
        $this->loginname            = $livestream_config['loginname'];
        $this->player_password      = $livestream_config['password'];
        $this->sender_url           = $livestream_config['sender_url'];
        $this->use_opencast         = $livestream_config['use_opencast'];
        $this->oc_player_url        = $livestream_config['oc_player_url'];
        $this->opencast_installed   = $this->plugin->checkOpenCast();

        $this->url_placeholder  = URLPLACEHOLDER;

    }

     /**
    * Inserts new player data or updates old data (streaming url, username, password)
    */
    public function admin_change_playerdata_action()
    {
        CSRFProtection::verifyUnsafeRequest();
        $loginname  = Request::get('loginname');
        $password   = Request::get('player_password');
        $sender_url = Request::get('sender_url');
        $player_url = Request::get('player_url');
        $use_opencast = Request::option('use_opencast');

        $oc_player_url = '';
        if ($use_opencast) {
            $oc_player_url = Request::get('oc_player_url');
        }

        $error = false;
        if (!$this->checkUrl($sender_url)) {
            PageLayout::postError($this->plugin->_('Stream-URL (Sender) ist ungültig.'));
            $error = true;
        }

        if (!$this->checkUrl($player_url)) {
            PageLayout::postError($this->plugin->_('Stream-URL (Empfänger) ist ungültig.'));
            $error = true;
        }

        if ($oc_player_url && !$this->checkUrl($oc_player_url)) {
            PageLayout::postError($this->plugin->_('Opencast Stream-URL (Empfänger) ist ungültig.'));
            $error = true;
        }

        if (empty($loginname) || empty($password)) {
            PageLayout::postError($this->plugin->_('Benutzername und Passwort dürfen nicht leer sein'));
            $error = true;
        }

        if (!$error) {
            $livestream_config['loginname'] = $loginname;
            $livestream_config['password'] = $password;
            $livestream_config['sender_url'] = trim(strtolower($sender_url));
            $livestream_config['player_url'] = trim(strtolower($player_url));
            if ($use_opencast && $this->plugin->checkOpenCast()) {
                $livestream_config['oc_player_url'] = trim(strtolower($oc_player_url));
                $livestream_config['use_opencast'] = true;
            } else {
                $livestream_config['use_opencast'] = false;
                //Clear all course in order to avoid conflict
                $livestreams = LiveStream::findAll();
                foreach ($livestreams as $livestream) {
                    if ($livestream->mode == MODE_OPENCAST) {
                        $livestream->mode = MODE_DEFAULT;
                        $livestream->store();
                    }
                }
            }
            LiveStream::setConfig($livestream_config);
            PageLayout::postSuccess($this->plugin->_('Die LiveStreaming Daten wurden erfolgreich gespeichert.'));
        }
        
        $this->redirect('admin/admin');
    }

    private function checkUrl($url) {
        $res = true;
        if (!filter_var($url, FILTER_VALIDATE_URL) || strpos($url, URLPLACEHOLDER) === FALSE) {
            $res = false;
        }
        return $res;
    }
}