<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

namespace App;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use App\Config as AppConfig;

class Application extends SilexApplication
{
    const ENV_DEV = "dev";
    const ENV_PROD = "prod";

    private $rootDir;
    private $env;

    public function __construct($env = "prod")
    {
        $this->rootDir = __DIR__ .'/../';
        $this->env = $env;

        parent::__construct();

        $config = AppConfig::getInstance()->get();
        $app = $this;

        $app['locale'] = $config->settings->locale;

        if($this->env === "dev")
        {
            $app['debug'] = true;
        }

        $app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__.'/logs/' . $this->env . '.' . date('Ym') . '.log',
        ));
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => array($this->rootDir . '/views'), // todo: add locale subfolder for localised templates eg: /views/pl
        ));
        $app->register(new ValidatorServiceProvider());
        $app->register(new SwiftmailerServiceProvider(), array(
            'swiftmailer.options' => array(
                'host' => $config->settings->swiftmailer->host,
                'port' => $config->settings->swiftmailer->port,
                'username' => $config->settings->swiftmailer->username,
                'password' => $config->settings->swiftmailer->password,
                'encryption' => $config->settings->swiftmailer->encryption,
                'auth_mode' => $config->settings->swiftmailer->auth_mode
            )
        ));

        $app->error(function (\Exception $e, Request $request, $code) use ($app) {
            $app['monolog']->error('Request failed with code: ' . $code . ' client: ' . $_SERVER['REMOTE_ADDR'] . ' message: ' . $e->getMessage());

            if(!file_exists(__DIR__ . '/../views/errors/' . $code . '.html.twig'))
            {
                $code = 500;
            }
            return new Response($app['twig']->render('/errors/' . $code . '.html.twig', array()));
        });

        $app->mount('', new ControllerProvider());
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function getEnv()
    {
        return $this->env;
    }


}