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
            'start' => new Page('start', '/ Witamy!'),
            'about' => new Page('about', '/ O firmie'),
            'contact' => new Page('contact', '/ Kontakt'),
            'contactSend' => new Page('contactSend', '/ Kontakt'),
            'services' => new Page('services', '/ Usługi'),
            'serviceCarwash' => new Page('serviceCarwash', '/ Usługi / Myjnia cystern'),
            'serviceRepair' => new Page('serviceRepair', '/ Usługi / Warsztat'),
            'serviceTransport' => new Page('serviceTransport', '/ Usługi / Transport'),
        );

        $this->layoutFile = 'layout.html.twig';

        $controllers = $app['controllers_factory'];

        // todo: validation
        // todo: $this->pages loop -> set handlers ?

        $controllers
            ->get('/', [$this, $this->pages['start']->name])
            ->bind($this->pages['start']->name);

        /*$controllers
            ->get('/status', [$this, 'status'])
            ->bind('status');*/

        $controllers
            ->get('/about/', [$this, $this->pages['about']->name])
            ->bind($this->pages['about']->name);

        $controllers
            ->get('/services/', [$this, $this->pages['services']->name])
            ->bind($this->pages['services']->name);

        $controllers
            ->get('/services/carwash/', [$this, $this->pages['serviceCarwash']->name])
            ->bind($this->pages['serviceCarwash']->name);

        $controllers
            ->get('/services/repair/', [$this, $this->pages['serviceRepair']->name])
            ->bind('serviceRepair');

        $controllers
            ->get('/services/transport/', [$this, $this->pages['serviceTransport']->name])
            ->bind($this->pages['serviceTransport']->name);

        $controllers
            ->get('/contact/', [$this, $this->pages['contact']->name])
            ->bind($this->pages['contact']->name);

        $controllers->post('/contact/', function (Request $request) {
            return $this->contactSend($request);
        })->bind($this->pages['contactSend']->name);

        return $controllers;
    }

    public function status()
    { // maintenance purpose only
        if(!$this->app['debug'])
        {
            return null; // todo - redirect to index
        }

        $output = 'App powered by Silex v2+';
        $output .= '<br>PHP version 7+';

        return $output;
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
            'action' => $webAction
        ));
    }

    public function contactSend(Request $request)
    {
        $webAction = new WebAction(true);
        $currentPage = $this->pages['contact'];
        $params = $request->request->all();
        $errors = $this->app['validator']->validate($params['contactEmail'], new Assert\Email());

        if (count($errors) > 0)
        {
            $webAction->setError($this->lang['error.email.invalid.address']);
        } else
        {
            $sender = new Sender($this->app);

            $webAction = $sender->sendEmail(array(
                'fromEmail' => $params['contactEmail'],
                'fromName' => $params['contactName'] . ' ' . $params['contactLastName'],
                'toEmail' => $this->appConfig->settings->contactEmail,
                'title' => $this->lang['contact.email.title'],
                'message' => $request->get('contactMessage')
            ));

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
                $this->app['monolog']->error('Confirmation email send failed. to: ' . $params['contactEmail'] . ' error: ' . $sendConfirmation->errors[0]);
            }
        }

        return $this->renderPage($currentPage, array(
            'actionCode' => $params['actionCode'],
            'action' => $webAction,
            'scrollTo' => '#contactDataSection'
        ));
    }

    private function renderPage(Page $page, $params = array())
    {
        $pageParams = array_merge(array(
            'currentPage' => $page->name,
            'title' => $page->getTitle(),
            'year' => date('Y')
        ), $params);

        return $this->app['twig']->render($this->layoutFile, $pageParams);
    }
}