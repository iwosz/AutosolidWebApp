<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;


class WebAction
{
    public $result;
    public $errors = array();

    function __construct($result)
    {
        $this->result = $result;
    }

    public function toArray()
    {
        return array('result' => $this->result, 'errors' => $this->errors);
    }

    public function setError($message)
    {
        $this->result = false;
        array_push($this->errors, $message);

        return $this;
    }
}