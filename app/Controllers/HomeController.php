<?php

namespace App\Controllers;

use App\Facades\JWT;
use App\Http\Request;
use App\Kernel\View;
use App\Annotations\DI;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Annotations\Middleware;
use App\Annotations\Route;
use App\Annotations\Autowired\Autowired;
use ReflectionClass;

class HomeController
{
    /**
     * @var Request
     * @Autowired("App\Http\Request")
     */
    public $request;

    /**
     * @param Request $request
     * @param AnnotationReader $reader
     * @return View
     *
     * @DI\Set({
     *  @DI\Item(name="request", value="request")
     * })
     */
    public function index($request, AnnotationReader $reader): View
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return View
     * @Middleware\Item("guest")
     * @Route\Get("/home/home")
     */
    public function home(Request $request): View
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return string
     *
     * @Route\Get("/jwt")
     */
    public function jwt(Request $request)
    {
        return JWT::decode($request->query('jwt'));
    }
}
