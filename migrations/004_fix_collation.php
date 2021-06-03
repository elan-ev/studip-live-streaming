<?php

class FixCollation extends Migration
{
    public function description()
    {
        return 'fix collation for livestream_seminar tables';
    }

    public function up()
    {
        $db = DBManager::get();
        $sql = "ALTER TABLE livestream_seminar
                CHANGE seminar_id seminar_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE mode mode enum('default', 'opencast') COLLATE latin1_bin NOT NULL";
        $db->exec($sql);
        SimpleORMap::expireTableScheme();

    }

    public function down()
    {
        // This migration works as a correction and there is no need for reverting (down)
    }
}
