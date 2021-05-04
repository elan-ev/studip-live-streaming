<?php

class AddCountdownOption extends Migration
{
    public function description()
    {
        return 'add countdown option to livestream_seminar table for live streaming plugin';
    }

    public function up()
    {
        //Alter Table
        $db = DBManager::get();
        $db->exec("ALTER TABLE livestream_seminar 
                    ADD COLUMN countdown_activated TINYINT NOT NULL DEFAULT 0,
                    ADD COLUMN countdown_timestamp INT(11) UNSIGNED NOT NULL DEFAULT 0");
        SimpleORMap::expireTableScheme();

    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec("ALTER TABLE livestream_seminar 
                    DROP COLUMN countdown_timestamp,
                    DROP COLUMN countdown_activated");
        SimpleORMap::expireTableScheme();
    }
}
