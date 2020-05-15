<?php

namespace App\Kernel;

use Closure;
use Exception;
use Generator;
use function dget;
use function dset;

class Task
{
    /**
     * @var int
     */
    protected $task_id;

    /**
     * @var Generator
     */
    protected $coroutine;

    /**
     * @var Scheduler
     */
    protected $scheduler;

    /**
     * @var Task
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $before = true;

    public function __construct(
        int $task_id,
        Generator $coroutine,
        Scheduler $scheduler,
        Task $parent = null
    ) {
        $this->task_id = $task_id;
        $this->coroutine = $coroutine;
        $this->scheduler = $scheduler;
        $this->parent = $parent;
    }

    public function getId(): int
    {
        return $this->task_id;
    }

    public function getReturn()
    {
        return $this->coroutine->getReturn();
    }

    public function then($data = null)
    {
        if ($this->before) {
            $this->before = false;
            return $this->coroutine->current();
        }

        return $this->coroutine->send($data);
    }

    public function exception(Exception $exception)
    {
        return $this->coroutine->throw($exception);
    }

    public function isDone(): bool
    {
        return !$this->coroutine->valid();
    }

    public function getParent(): Task
    {
        return $this->parent;
    }

    public function getRoot(): Task
    {
        $task = $this;
        while ($task->getParent() !== null) {
            $task = $task->getParent();
        }
        return $task;
    }

    public static function getContext(string $key, $default = null): SystemCall
    {
        return new SystemCall(function (Task $task, Scheduler $scheduler) use (
            $key,
            $default
        ) {
            return dget($scheduler->getContext(), $key, $default);
        });
    }

    public static function setContext(string $key, $value): SystemCall
    {
        return new SystemCall(function (Task $task, Scheduler $scheduler) use (
            $key,
            $value
        ) {
            return dset($scheduler->getContext(), $key, $value);
        });
    }

    public static function getTask(): SystemCall
    {
        return new SystemCall(function (Task $task, Scheduler $scheduler) {
            return $task;
        });
    }

    public static function getScheduler(): SystemCall
    {
        return new SystemCall(function (Task $task, Scheduler $scheduler) {
            return $scheduler;
        });
    }

    public static function newTask($generator): SystemCall
    {
        if ($generator instanceof Closure) {
            $generator = $generator();
        }
        return new SystemCall(function (Task $task, Scheduler $scheduler) use (
            $generator
        ) {
            return new Task(
                $scheduler->getTaskId(),
                $generator,
                $scheduler,
                $task
            );
        });
    }
}
