<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;


class Page
{
    public $name;
    public $title;

    function __construct($name, $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function getTitle()
    {
        return !empty($this->title) ? $this->title : '';
    }
}