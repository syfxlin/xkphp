<?php

namespace App\Kernel;

use Generator;

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
     * @var mixed
     */
    protected $data;

    /**
     * @var bool
     */
    protected $before = true;

    public function __construct(int $task_id, Generator $coroutine)
    {
        $this->task_id = $task_id;
        $this->coroutine = $coroutine;
    }

    public function getId(): int
    {
        return $this->task_id;
    }

    public function send($data): Task
    {
        $this->data = $data;
        return $this;
    }

    public function then()
    {
        if ($this->before) {
            $this->before = false;
            return $this->coroutine->current();
        }

        $result = $this->coroutine->send($this->data);
        $this->data = null;
        return $result;
    }

    public function isDone(): bool
    {
        return !$this->coroutine->valid();
    }
}
