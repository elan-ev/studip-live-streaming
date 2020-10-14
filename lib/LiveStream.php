<?php
/**
 * LiveStream.php - LiveStream class for Stud.IP
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class LiveStream extends \SimpleORMap
{
    static $config;

    protected static function configure($config = [])
    {
        $config['db_table'] = 'livestream_seminar';

        parent::configure($config);
    }

    static function loadConfig()
    {
        if (!self::$config) {
            self::$config = json_decode(\Config::get()->getValue('LS_CONFIG'), true);
        }
    }

    static function getConfig()
    {
        self::loadConfig();

        return self::$config;
    }

    static function setConfig($configs)
    {
        \Config::get()->store('LS_CONFIG', json_encode($configs));
    }

     /**
     * Finds all livestreams.
     *
     * @return LiveStream[]
     */
    public static function findAll()
    {
        return static::findBySQL('mode IS NOT NULL');
    }
}
