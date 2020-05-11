<?php

namespace App\Controllers;

use App\Annotations\Autowired\Autowired;
use App\Annotations\DI;
use App\Annotations\Route;
use App\Aspect\LogAspect;
use App\Events\LogEvent;
use App\Exceptions\Http\MethodNotAllowedException;
use App\Facades\App;
use App\Facades\Cookie;
use App\Facades\Event;
use App\Facades\JWT;
use App\Facades\Lang;
use App\Facades\Log;
use App\Http\Request;
use App\Http\Response;
use App\Kernel\Scheduler;
use App\Kernel\View;
use App\Listeners\LogListener;
use App\Listeners\LogSubscriber;
use App\Listeners\StrListener;
use App\Utils\Crypt;
use App\Utils\Hash;
use Doctrine\Common\Annotations\AnnotationReader;
use function __;
use function abort;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function preg_replace;
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
        return view('home')->filter(function (string $content) {
            return preg_replace('/>(\s*)</', '><', $content);
        });
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
        Log::info('Info', ['Info']);
        Log::debug('Debug', ['Debug']);
        Log::warning('Warn', ['Warn']);
        Log::error('Error', [
            'error' => new MethodNotAllowedException("Error"),
        ]);
        Log::alert('Alert', [
            'error' => new MethodNotAllowedException("Error"),
        ]);
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
        report('debug', 'hash');
        $hash = \App\Facades\Hash::make('123');
        report('debug', 'encrypt');
        $encrypt = App::callWithAspect(
            [Crypt::class, 'encrypt'],
            [
                'value' => '123',
            ]
        );
        report('debug', 'function');
        App::callWithAspect(
            function () {
                report('debug', 'function-in');
            },
            [],
            null,
            false,
            [LogAspect::class]
        );
        report('debug', App::make('path'));
        return '';
    }

    /**
     * @return string
     *
     * @Route\Get("/event")
     */
    public function event(): string
    {
        Event::dispatch('event.str_config');
        Event::listen(LogEvent::class, [LogListener::class, 'handle']);
        Event::subscribe(LogSubscriber::class);
        Event::dispatch(LogEvent::class);
        Event::listen('event.str', StrListener::class);
        Event::dispatch('event.str');
        return '';
    }

    /**
     * @return string
     *
     * @Route\Get("/task")
     */
    public function task(): string
    {
        $scheduler = new Scheduler();
        $req = function () {
            report('debug', 'task1-start');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://ixk.me");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            report('debug', 'task1-end');
            return $data;
        };
        $task1 = function () use ($req) {
            for ($i = 0; $i < 5; $i++) {
                yield $req();
            }
        };
        $task2 = function () {
            for ($i = 0; $i < 5; $i++) {
                report('debug', 'task2-start');
                yield;
            }
        };
        $scheduler->add($task1);
        $scheduler->add($task2);
        $scheduler->then();
        return '';
    }

    /**
     * @return string
     *
     * @Route\Get("/lang")
     */
    public function locale(): string
    {
        $res = Lang::trans("message.msg", ['str' => 'string', 'int' => 123]);
        $res .= __("message.not", [], "default");
        return $res;
    }
}
