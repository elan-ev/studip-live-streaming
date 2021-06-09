<?php

class AlterCountdownTimestampsAddSession extends Migration
{
    public function description()
    {
        return 'alter countdown_timestamp column to session_start and add session_end column to livestream_seminar table for live streaming plugin';
    }

    public function up()
    {
        $db = DBManager::get();
        //Rename countdown_timestamp to session_start
        $db->exec("ALTER TABLE livestream_seminar
                    CHANGE countdown_timestamp session_start INT(11) UNSIGNED NOT NULL DEFAULT 0");
        
        //Add session_end
        $db->exec("ALTER TABLE livestream_seminar
                    ADD COLUMN session_end INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER session_start");
        SimpleORMap::expireTableScheme();

    }

    public function down()
    {
        $db = DBManager::get();
        //Rename session_start to countdown_timestamp
        $db->exec("ALTER TABLE livestream_seminar
            CHANGE session_start countdown_timestamp INT(11) UNSIGNED NOT NULL DEFAULT 0");
        //Remove session_end
        $db->exec("ALTER TABLE livestream_seminar 
                    DROP COLUMN session_end");
        SimpleORMap::expireTableScheme();
    }
}
