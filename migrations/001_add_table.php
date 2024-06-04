<?php

class AddTable extends Migration
{
    public function description()
    {
        return 'add config and table for live streaming plugin';
    }

    public function up()
    {
        //Create Table
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS `livestream_seminar` (
          `seminar_id` VARCHAR( 32 ) NOT NULL ,
          `mode` VARCHAR(10) NOT NULL,
          PRIMARY KEY (  `seminar_id` )
        )");
        SimpleORMap::expireTableScheme();

        //Create Config
        try {
            if (StudipVersion::olderThan('4.2')) {
                $query = "REPLACE INTO `config`
                (`field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`, `description`)
                VALUES (:field, :value, :type, 'global', 'meetings',
                        UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
            } else {
                $query = "REPLACE INTO `config`
                (`field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`, `description`)
                VALUES (:field, :value, :type, 'global', 'meetings',
                        UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
            }

            $statement = DBManager::get()->prepare($query);

            $statement->execute([
                ':field' => 'LS_CONFIG',
                ':value' => '',
                ':type'  => 'string',
                ':description' => 'Konfiguration des LiveStreaming-Plugins im JSON Format']
            );
        } catch (InvalidArgumentException $ex) {

        }

    }

    public function down()
    {
        $db = DBManager::get();
        //Remove Config
        $db->exec("DELETE FROM config WHERE field = 'LS_CONFIG'");

        //Drop Table
        $db->exec(sprintf('DROP TABLE IF EXISTS `%s`', 'livestream_seminar'));
        
        SimpleORMap::expireTableScheme();


    }
}
