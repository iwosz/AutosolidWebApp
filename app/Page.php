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
    private $name;
    private $title;
    private $url;

    function __construct($url, $name, $title)
    {
        $this->name = $name;
        $this->title = $title;
        $this->url = $url;
    }

    public function getTitle()
    {
        return !empty($this->title) ? $this->title : '';
    }

    public function getName()
    {
        return !empty($this->name) ? $this->name : '';
    }

    public function getUrl()
    {
        return !empty($this->url) ? $this->url : '';
    }
}