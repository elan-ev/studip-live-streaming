<?php

class AddOptionsField extends Migration
{
    public function description()
    {
        return 'Adds options field to livestream_seminar table for a flexible addition of options';
    }

    public function up()
    {
        $db = DBManager::get();
        $db->exec("ALTER TABLE livestream_seminar 
                    ADD COLUMN options TEXT DEFAULT NULL AFTER countdown_timestamp");
        SimpleORMap::expireTableScheme();

    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec("ALTER TABLE livestream_seminar 
                    DROP COLUMN options");
        SimpleORMap::expireTableScheme();
    }
}
