<?php

class AddTable extends Migration
{
    public function description()
    {
        return 'add table for stream player';
    }

    public function up()
    {
        $db = DBManager::get();

        $db->exec('CREATE TABLE IF NOT EXISTS `stream_player` (
          `player_id` VARCHAR(32) NOT NULL PRIMARY KEY,
          `loginname` VARCHAR(32) NOT NULL,
          `password` VARCHAR(32) NOT NULL,
          `sender_url` VARCHAR(255) NOT NULL,
          `player_url` VARCHAR(255) NOT NULL,
          `filename` VARCHAR(32) NOT NULL,
          `mkdate` INT(11) NOT NULL DEFAULT 0,
          `chdate` INT(11) NOT NULL DEFAULT 0
        )');

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec(sprintf('DROP TABLE IF EXISTS `%s`', 'stream_player'));

        SimpleORMap::expireTableScheme();
    }
}
