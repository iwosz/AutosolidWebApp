<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;

use Silex\Application as SilexApplication;
use App\WebAction as WebAction;
use App\Lang as Lang;

class Sender
{
    private $app;
    private $lang;

    public function __construct(SilexApplication $app)
    {
        $this->app = $app;
        $this->lang = Lang::getInstance()->get();
    }

    public function sendEmail($params = array())
    {
        $webAction = new WebAction(true);

        try
        {
            $message = (new \Swift_Message($params['title']))
                ->setFrom([$params['fromEmail'] => $params['fromName']])
                ->setTo([$params['toEmail']])
                ->setBody($params['message'], 'text/html');

            $result = $this->app['mailer']->send($message);

            if(!$result)
            {
                $this->app['monolog']->error('Email send failed to ' . $params['toEmail']);
                $webAction->setError($this->lang['error.email.failed.send']);
            }
        } catch (\Exception $ex)
        {
            $this->app['monolog']->error('Email send Exception: ' . $ex->getMessage());
            $webAction->setError($this->lang['error.server.internal']);
        }

        return $webAction;
    }
}