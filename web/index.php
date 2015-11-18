<?php

use Zaibatsu\Providers\UserProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Zaibatsu\App();

$app['debug'] = true;

if ($app['debug'] === true) {
    error_reporting(E_ALL ^ E_NOTICE);
    ini_set('display_errors', 'On');
}

$app->register(new Sorien\Provider\PimpleDumpProvider());

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => '../app/views'
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'zaibatsu',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8',
    ),
));

$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'secured' => array(
        'pattern' => '^/admin',
        'form' => array('login_path' => '/login/', 'check_path' => '/admin/check/'),
        'logout' => array('logout_path' => '/admin/logout/', 'invalidate_session' => true, 'target' => '/login/'),
        //'remember_me' => array('key' => '12345'),
        'users' => $app->share(function () use ($app) {
            return new UserProvider($app['db']);
        }),
    ),
);

$loader = new YamlFileLoader(new FileLocator(__DIR__ . '/../app/config'));
$collection = $loader->load('routes.yml');
$app['routes']->addCollection($collection);

if ($app['debug'] !== true) {
    $app->error(function (Exception $e) use ($app) {
        if ($e instanceof NotFoundHttpException) {
            return $app->render('pages/error.twig', array('code' => 404));
        }

        $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;

        return $app->render('pages/error.twig', array('code' => $code));
    });
}

$app->run();
