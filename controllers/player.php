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
    * Displays StreamPlayer data (streaming URLs, username, password) and
    * lets the admin change the data.
    *
    */
    public function admin_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('admin', Context::getId())) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }
        
        Navigation::activateItem('/course/livestreaming/admin');
        
        $this->title = _('LiveStreaming Daten');
        
        // fetch access data for stream (url, filename, login data)
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT * FROM stream_player WHERE player_id = ?");
        $stmt->execute([Context::getId()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->player_url       = $result['player_url'];
        $this->filename         = $result['filename'];
        $this->loginname        = $result['loginname'];
        $this->player_password  = $result['password'];
        $this->sender_url       = $result['sender_url'];
    }
    
    /**
    * Inserts new player data or updates old data (streaming url, username, password)
    */
    public function admin_change_playerdata_action()
    {
        global $perm;
        CSRFProtection::verifyUnsafeRequest();
        if(!$perm->have_studip_perm('admin', Context::getId())) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

        $loginname  = Request::option('loginname');
        $password   = Request::option('player_password');
        $sender_url = Request::get('sender_url');
        $player_url = Request::get('player_url');
        $filename   = Request::get('filename');
        
        // TODO: URL and filename check here
        
        // save to db
        $db = DBManager::get();

        $sql = "REPLACE INTO 
                    stream_player (player_id, loginname, password, sender_url, player_url, filename)
                VALUES
                    (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([Context::getId(), $loginname, $password, $sender_url, $player_url, $filename]);
        
        PageLayout::postSuccess('Die LiveStreaming Daten wurden erfolgreich gespeichert.');
        $this->redirect('player/admin');
    }

    /**
    * Displays the player and access data for the stream for lecturers.
    */
    public function teacher_action()
    {
        global $perm;
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }
        
        Navigation::activateItem('/course/livestreaming/teacher');
        
        $this->title = _('LiveStreaming Übersicht');
        
        $db = DBManager::get();
        $stmt = $db->prepare("SELECT * FROM stream_player WHERE player_id = ?");
        $stmt->execute([Context::getId()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->player_username  = $result['loginname'];
        $this->player_password  = $result['password'];
        $this->sender_url       = $result['sender_url'];
        $this->player_url       = $result['player_url'];
        $this->filename         = $result['filename'];

    }

    /**
    * Displays the player for students.
    */
    public function student_action()
    {
        Navigation::activateItem('/course/livestreaming/student');
        
        $this->title = _('LiveStreaming');
       
    }
    
}
