<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;


class Lang
{
    public static $dictionary;

    private static $instance;

    protected function __construct()
    {

    }

    public static function getInstance()
    {
        if (null === static::$instance)
        {
            static::$instance = new Lang();
        }
        return static::$instance;
    }

    public static function get($locale = "pl")
    {
        static::$dictionary = Lang::getDictionaryFromFile($locale);

        return static::$dictionary;
    }

    private static function getDictionaryFromFile($locale)
    {
        $jsonString = file_get_contents(__DIR__.'/lang/lang.'.$locale.'.json');

        if(!empty($jsonString))
        {
            return json_decode($jsonString, true);
        } else
        {
            throw new \Exception("Lang file missing for locale " . $locale);
        }
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }
}