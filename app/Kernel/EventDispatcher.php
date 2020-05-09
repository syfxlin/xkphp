<?php

namespace App\Kernel;

use App\Application;
use App\Contracts\Event;
use Closure;
use function class_exists;
use function get_class;
use function is_array;
use function is_string;
use function str_stringify_callback;

class EventDispatcher
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $subscribers = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function listen(string $event, $listener): void
    {
        $this->listeners[$event][] = $this->makeListener($listener);
    }

    public function unlisten(string $event): void
    {
        $this->listeners[$event] = [];
    }

    public function has(string $event): bool
    {
        return isset($this->listeners[$event]);
    }

    public function subscribe(string $subscriber): void
    {
        $subscribers = $this->app->call(
            [$subscriber, 'subscribe'],
            [
                'dispatcher' => $this,
            ]
        );
        $this->subscribers[$subscriber] = $subscribers;
        foreach ($subscribers as $event => $subs) {
            foreach ($subs as $sub) {
                $this->listen($event, $sub);
            }
        }
    }

    public function unsubscribe(string $subscriber): void
    {
        foreach ($this->subscribers[$subscriber] ?? [] as $event => $subs) {
            $this->unlisten($event);
        }
    }

    /**
     * @param Event|string $event
     * @param array $args
     * @return array
     */
    public function dispatch($event, $args = []): array
    {
        $event_name = null;
        if (is_string($event)) {
            $event_name = $event;
            $event = class_exists($event) ? new $event() : $event;
        } else {
            $event_name = get_class($event);
        }

        $results = [];

        foreach ($this->getListener($event_name) as $listener) {
            $result = $listener($event, $args);
            if ($result === false) {
                break;
            }
            $results[] = $result;
        }

        return $results;
    }

    protected function makeListener($listener): Closure
    {
        if (is_string($listener) || is_array($listener)) {
            return function ($event, $args) use ($listener) {
                return $this->app->call(
                    str_stringify_callback($listener, 'handle'),
                    [
                        'event' => $event,
                        'args' => $args,
                    ]
                );
            };
        }
        return function ($event, $args) use ($listener) {
            return $this->app->call($listener, [
                'event' => $event,
                'args' => $args,
            ]);
        };
    }

    public function getListener(string $event)
    {
        return $this->listeners[$event] ?? [];
    }
}
