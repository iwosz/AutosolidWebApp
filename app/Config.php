<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;


class Config
{
    public static $config;

    private static $instance;
    private static $configFilePath = __DIR__."/../appconfig.json";

    protected function __construct()
    {
        static::$config = Config::getConfigFromFile();
    }

    public static function getInstance()
    {
        if (null === static::$instance)
        {
            static::$instance = new Config();
        }
        return static::$instance;
    }

    public static function get()
    {
        if(!static::$config)
        {
            static::$config = Config::getConfigFromFile();
        }

        return static::$config;
    }

    private static function getConfigFromFile()
    {
        $jsonString = file_get_contents(Config::$configFilePath);

        if(!empty($jsonString))
        {
            return json_decode($jsonString);
        } else
        {
            throw new \Exception("Config file missing in path " . Config::$configFilePath);
        }
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }
}