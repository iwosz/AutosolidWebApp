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
use App\plugins\EmailValidator as EmailValidator;

class Sender
{
    private $app;
    private $lang;
    private $EmailValidator;
    private $vetifiedEmails;

    public function __construct(SilexApplication $app)
    {
        $this->app = $app;
        $this->lang = Lang::getInstance()->get();
        $this->EmailValidator = new EmailValidator();
        //$this->EmailValidator->Debug = true;
        $this->verifiedEmails = array('hello@maciejwasiak.com', 'biuro@autosolid.eu');
    }

    public function sendEmail($params = array())
    {
        $webAction = new WebAction(true);

        $this->app['monolog']->warning(__CLASS__ . ' > fromEmail: ' . $params['fromEmail'] . ' toEmail: ' . $params['toEmail']);

        try
        {
            $verified = in_array($params['fromEmail'], $this->verifiedEmails) or $this->EmailValidator->check($params['fromEmail']);

            if($verified)
            {
                $message = (new \Swift_Message($params['title']))
                    ->setFrom([$params['fromEmail'] => $params['fromName']])
                    ->setTo([$params['toEmail']])
                    ->setBody($params['message'], 'text/html');

                $result = $this->app['mailer']->send($message);

                if(!$result)
                {
                    $this->app['monolog']->error(__CLASS__ . ' > Email send failed to ' . $params['toEmail']);
                    $webAction->setError($this->lang['error.email.failed.send']);
                } else
                {
                    $this->app['monolog']->warning(__CLASS__ . ' > Email send successed to ' . $params['toEmail']);
                }
            } else
            {
                $this->app['monolog']->warning(__CLASS__ . ' > Email not exists (fake/spam): ' . $params['fromEmail']);
                $webAction->setError($this->lang['error.email.invalid.address']);
            }
        } catch (\Exception $ex)
        {
            $this->app['monolog']->error(__CLASS__ . ' > Email send Exception: ' . $ex->getMessage());
            $webAction->setError($this->lang['error.server.internal']);
        }

        return $webAction;
    }
}