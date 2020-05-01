<?php

namespace App\Kernel;

use Closure;
use Generator;
use function array_shift;

class Scheduler
{
    /**
     * @var Task[]
     */
    protected $tasks = [];

    /**
     * @var int
     */
    protected $task_id = 1;

    /**
     * @param Task|Generator|Closure $task
     * @return $this
     */
    public function add($task): Scheduler
    {
        if ($task instanceof Closure) {
            $task = $task();
        }
        if ($task instanceof Generator) {
            $this->tasks[] = new Task($this->task_id++, $task);
        } else {
            $this->tasks[] = $task;
        }
        return $this;
    }

    public function then(): array
    {
        $result = [];
        while (!empty($this->tasks)) {
            $task = array_shift($this->tasks);
            $result[] = $task->then();

            if (!$task->isDone()) {
                $this->tasks[] = $task;
            }
        }
        return $result;
    }
}
