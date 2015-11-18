<?php

namespace Zaibatsu\Controllers;

use Zaibatsu\App;
use Zaibatsu\Controller;

/**
 * Class AdminController
 * @package Zaibatsu\Controllers
 */
class AdminController extends Controller
{
    /**
     * Панель администрирования
     *
     * @param App $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(App $app)
    {
        return $app->render('pages/admin.twig');
    }
}