<?php

namespace App\Controllers;

use App\Exceptions\Http\MethodNotAllowedException;
use App\Exceptions\HttpStatusException;
use App\Facades\Console;
use App\Facades\Cookie;
use App\Facades\JWT;
use App\Http\Request;
use App\Http\Response;
use App\Http\Stream;
use App\Kernel\View;
use App\Annotations\DI;
use App\Utils\Hash;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Annotations\Middleware;
use App\Annotations\Route;
use App\Annotations\Autowired\Autowired;
use ReflectionClass;
use RuntimeException;
use function abort;
use function report;

class HomeController
{
    /**
     * @var Request
     * @Autowired("App\Http\Request")
     */
    public $request;

    /**
     * @var Hash
     */
    public $hash;

    public function __construct(Hash $hash)
    {
        // 如果不使用注解注入类属性，则可以使用构造器注入
        $this->hash = $hash;
    }

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
     * @Route\Get("/home/home")
     */
    public function home(Request $request): string
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return string
     *
     * @Route\Get("/jwt")
     */
    public function jwt(Request $request): string
    {
        return JWT::decode($request->query('jwt'));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function get(Request $request): bool
    {
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     *
     * @Route\Get("/exce")
     */
    public function exception(Request $request): bool
    {
        Console::info('Info', 'Info', ['Info']);
        Console::debug('Debug', 'Debug', ['Debug']);
        Console::warn('Warn', 'Warn', ['Warn']);
        Console::error(new MethodNotAllowedException('Error'));
        Console::fatal(new MethodNotAllowedException('Fatal'));
        report('info', 'Info Function');
        abort(403);
        return true;
    }

    /**
     * @param int $path
     * @param int $query
     * @return string
     *
     * @Route\Get("/inject/{path}")
     */
    public function inject(int $path, int $query): string
    {
        // IoC 容器会自动将参数名作为 key 在绑定的实例和 Request 中寻找匹配的字段，然后进行注入
        return $path . ',' . $query;
    }

    /**
     * @return Response
     *
     * @Route\Get("/cookie")
     */
    public function cookie(): Response
    {
        $response = \response('Cookie')->cookie('cookie1', 'value');
        Cookie::queue(\App\Http\Cookie::make('cookie2', 'value'));
        return $response;
    }

    /**
     * @return string
     *
     * @Route\Get("/aspect")
     */
    public function aspect(): string
    {
        //        $proxy = new AspectProxy(new Test());
        //        /* @var Test $proxy */
        //        $proxy->aspect('args');
        $hash = \App\Facades\Hash::make('123');
        return '';
    }
}

class Test
{
    public function aspect($args): string
    {
        report('info', 'Test->aspect');
        // throw new Exception('Aspect Exception');
        return "return-$args";
    }
}
