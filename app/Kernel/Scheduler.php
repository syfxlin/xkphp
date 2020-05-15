<?php

namespace App\Kernel;

use Closure;
use Exception;
use Generator;
use SplQueue;
use SplStack;

class Scheduler
{
    /**
     * @var SplQueue<Task>
     */
    protected $tasks;

    /**
     * @var int
     */
    protected $task_id = 1;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var array
     */
    protected $context = [];

    public function __construct()
    {
        $this->tasks = new SplQueue();
    }

    /**
     * @param Task|Generator|Closure $task
     * @param Task|null $parent
     * @return $this
     */
    public function add($task, Task $parent = null): Scheduler
    {
        if ($task instanceof Closure) {
            $task = $task();
        }
        if ($task instanceof Generator) {
            $task = new Task($this->task_id++, $task, $this, $parent);
        }
        $this->tasks->enqueue($task);
        return $this;
    }

    public function then()
    {
        $return_values = [];
        $stack = new SplStack();
        $result = null;
        $exception = null;
        while (!$this->tasks->isEmpty()) {
            $task = $this->tasks->dequeue();
            while (true) {
                try {
                    if ($exception !== null) {
                        $task->exception($exception);
                        $exception = null;
                        continue;
                    }

                    $return = $task->then($result);

                    if ($return instanceof SystemCall) {
                        $return = $return($task, $this);
                    }

                    if ($return === $task) {
                        $result = $return;
                        continue;
                    }

                    if ($return instanceof Generator) {
                        $return = new Task(
                            $this->task_id++,
                            $return,
                            $this,
                            $task
                        );
                    }

                    if ($return instanceof Task) {
                        $stack->push($task);
                        $task = $return;
                        continue;
                    }

                    $result = $return;

                    if ($stack->isEmpty()) {
                        break;
                    }

                    if ($task->isDone()) {
                        // 获取返回值
                        $return = $task->getReturn();
                        /* @var Task $task */
                        $task = $stack->pop();
                        $result = $return;
                        continue;
                    }
                } catch (Exception $e) {
                    if ($stack->isEmpty()) {
                        throw $e;
                    }
                    $task = $stack->pop();
                    $exception = $e;
                }
            }
            if (!$task->isDone()) {
                $this->add($task);
            } else {
                $return_values[] = $task->getReturn();
            }
        }
        if ($exception !== null) {
            throw $exception;
        }
        return $return_values;
    }

    public function &getContext(): array
    {
        return $this->context;
    }

    public function getTaskId(): int
    {
        return $this->task_id++;
    }
}
