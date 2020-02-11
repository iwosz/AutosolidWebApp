<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;

use Silex\Application as SilexApplication;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Config as AppConfig;
use App\WebAction as WebAction;
use App\Page as Page;
use App\Lang as Lang;
use App\Sender as Sender;

class ControllerProvider implements ControllerProviderInterface
{
    private $app;
    private $appConfig;
    private $lang;
    private $pages;
    private $layoutFile;

    public function connect(SilexApplication $app)
    {
        $this->app = $app;
        $this->appConfig = AppConfig::getInstance()->get();
        $this->lang = Lang::getInstance()->get();
        $this->pages = array(
            'start' => new Page('/', 'start','/ Witamy!'),
            'about' => new Page('/about/', 'about', '/ O firmie'),
            'contact' => new Page('/contact/', 'contact', '/ Kontakt'),
            'services' => new Page('/services/', 'services', '/ Usługi'),
            'serviceCarwash' => new Page('/services/carwash/', 'serviceCarwash', '/ Usługi / Myjnia cystern'),
            'serviceRepair' => new Page('/services/repair/', 'serviceRepair', '/ Usługi / Warsztat'),
            'serviceTransport' => new Page('/services/transport/', 'serviceTransport', '/ Usługi / Transport'),
        );
        $this->layoutFile = 'layout.html.twig';

        $controllers = $app['controllers_factory'];

        /* @var $page Page */
        foreach($this->pages as $action => $page)
        {
            $controllers
                ->get($page->getUrl(), [$this, $action])
                ->bind($action);
        }

        $controllers->post('/contact/', function (Request $request) {
            return $this->contactSend($request);
        })->bind('contactSend');

        return $controllers;
    }

    public function start()
    {
        $currentPage = $this->pages['start'];

        return $this->renderPage($currentPage);
    }

    public function about()
    {
        $currentPage = $this->pages['about'];

        return $this->renderPage($currentPage);
    }

    public function services()
    {
        $currentPage = $this->pages['services'];

        return $this->renderPage($currentPage);
    }

    public function serviceCarwash()
    {
        $currentPage = $this->pages['serviceCarwash'];

        return $this->renderPage($currentPage);
    }

    public function serviceRepair()
    {
        $currentPage = $this->pages['serviceRepair'];

        return $this->renderPage($currentPage);
    }

    public function serviceTransport()
    {
        $currentPage = $this->pages['serviceTransport'];

        return $this->renderPage($currentPage);
    }

    public function contact()
    {
        $currentPage = $this->pages['contact'];
        $webAction = new WebAction(true);
        return $this->renderPage($currentPage, array(
            'actionCode' => '',
            'action' => $webAction,
            'recaptchaKey' => $this->appConfig->settings->recaptcha->publicKey
        ));
    }

    public function contactSend(Request $request)
    {
        $webAction = new WebAction(true);
        $currentPage = $this->pages['contact'];

        $params = $request->request->all();

        if(!empty($params['contactEmail']) /*or !$this->verifyEmailAddress($params['contactEmail'])*/)
        {
            $this->app['monolog']->warning('ContactSend > Spambot blocked. email: ' . $params['contactEmail'] . ' ip: ' . $_SERVER['REMOTE_ADDR']);

            return $this->renderPage($currentPage, array(
                'actionCode' => '',
                'action' => $webAction,
                'recaptchaKey' => $this->appConfig->settings->recaptcha->publicKey
            ));
        }

        $params['contactName'] = $params['contact-value1'];
        $params['contactLastName'] = $params['contact-value2'];
        $params['contactEmail'] = $params['contact-value3'];
        $params['contactMessage'] = $params['contact-value4'];

        $errors = $this->app['validator']->validate($params['contactEmail'], new Assert\Email());

        if (empty($params['contactEmail']) or count($errors) > 0)
        {
            $webAction->setError($this->lang['error.email.invalid.address']);
        } else if(!$params['recaptchaToken'] or !$this->verifyRecaptcha($params['recaptchaToken']))
        {
            $webAction->setError($this->lang['error.email.invalid.token']);
        } else
        {
            $sender = new Sender($this->app);

            $webAction = $sender->sendEmail(array(
                'fromEmail' => $params['contactEmail'],
                'fromName' => $params['contactName'] . ' ' . $params['contactLastName'],
                'toEmail' => $this->appConfig->settings->contactEmail,
                'title' => $this->lang['contact.email.title'],
                'message' => $params['contactMessage']
            ));

            if($webAction->result)
            {
                $confirmationMessage = str_replace(
                    array('{name}', '{contactEmail}'),
                    array($params['contactName'] . ' ' . $params['contactLastName'], $this->appConfig->settings->contactEmail),
                    $this->lang['contact.confirmation.message']
                );
                $sendConfirmation = $sender->sendEmail(array(
                    'fromEmail' => $this->appConfig->settings->contactEmail,
                    'fromName' => 'Autosolid s.c.',
                    'toEmail' => $params['contactEmail'],
                    'title' => $this->lang['contact.confirmation.title'],
                    'message' => $confirmationMessage
                ));

                if(!$sendConfirmation->result)
                {
                    $this->app['monolog']->error('ContactSend > Confirmation email send failed. to: ' . $params['contactEmail'] . ' error: ' . $sendConfirmation->errors[0]);
                }
            }
        }

        return $this->renderPage($currentPage, array(
            'actionCode' => $params['actionCode'],
            'action' => $webAction,
            'recaptchaKey' => $this->appConfig->settings->recaptcha->publicKey,
            'scrollTo' => '#contactDataSection'
        ));
    }

    private function renderPage(Page $page, $params = array())
    {
        $pageParams = array_merge(array(
            'currentPage' => $page->getName(),
            'title' => $page->getTitle(),
            'year' => date('Y')
        ), $params);

        return $this->app['twig']->render($this->layoutFile, $pageParams);
    }

    private function verifyRecaptcha($token)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => $this->appConfig->settings->recaptcha->secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseKeys = json_decode($response,true);

        return $responseKeys["success"] ? true : false;
    }

    private function verifyEmailAddress($email)
    {
        $valid = false;
        $domain = substr($email, strpos($email, '@') + 1);

        if (checkdnsrr($domain) !== false)
        {
            $valid = true;
        }

        return $valid;
    }
}