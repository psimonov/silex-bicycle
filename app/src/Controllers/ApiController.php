<?php

namespace Zaibatsu\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zaibatsu\Controller;

/**
 * Class AdminController
 * @package Zaibatsu\Controllers
 */
class ApiController extends Controller
{
    public function listAction()
    {
        $json = json_decode(@file_get_contents('http://rest-service.guides.spring.io/greeting'), true);

        return JsonResponse::create($json);
    }
}