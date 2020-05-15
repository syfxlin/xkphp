<?php

namespace Test\Unit;

use App\Kernel\Scheduler;
use App\Kernel\Task;
use Exception;
use Test\TestCase;

class TaskTest extends TestCase
{
    public function testScheduler(): void
    {
        $fun1 = function () {
            for ($i = 1; $i <= 3; ++$i) {
                echo "Task1 $i\n";
                yield; // 主动让出CPU的执行权
            }
        };
        $fun2 = function () use ($fun1) {
            for ($i = 1; $i <= 5; ++$i) {
                echo "Task2 $i\n";
                yield; // 主动让出CPU的执行权
            }
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun1);
        $scheduler->add($fun2);
        $scheduler->then();
        $this->expectOutputString(
            "Task1 1\nTask2 1\nTask1 2\nTask2 2\nTask1 3\nTask2 3\nTask2 4\nTask2 5\n"
        );
    }

    public function testReturnValue(): void
    {
        $fun1 = function () {
            yield;
            return 2;
        };
        $fun2 = function () use ($fun1) {
            $result = (yield $fun1());
            echo $result;
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun2);
        $scheduler->then();
        $this->expectOutputString("2");
    }

    public function testException(): void
    {
        $fun1 = function () {
            throw new Exception("Error");
            yield;
        };
        $fun2 = function () use ($fun1) {
            try {
                yield $fun1();
            } catch (Exception $exception) {
                echo $exception->getMessage();
            }
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun2);
        $scheduler->then();
        $this->expectOutputString("Error");
    }

    public function testContext(): void
    {
        $fun1 = function () {
            yield Task::setContext('data', 'value');
        };
        $fun2 = function () use ($fun1) {
            yield $fun1();
            $result = (yield Task::getContext('data'));
            $result .= (yield Task::getContext('data'));
            echo $result;
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun2);
        $scheduler->then();
        $this->expectOutputString("valuevalue");
    }

    public function testGetTask(): void
    {
        $fun1 = function () {
            /* @var Task $task */
            $task = (yield Task::getTask());
            $this->assertInstanceOf(Task::class, $task);
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun1);
        $scheduler->then();
    }

    public function testGetScheduler(): void
    {
        $fun1 = function () {
            /* @var Scheduler $sch */
            $sch = (yield Task::getScheduler());
            $this->assertInstanceOf(Scheduler::class, $sch);
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun1);
        $scheduler->then();
    }

    public function testSubTask(): void
    {
        $fun1 = function () {
            yield;
            return 1;
        };
        $fun2 = function () use ($fun1) {
            $result = (yield Task::newTask($fun1));
            echo $result;
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun2);
        $scheduler->then();
        $this->expectOutputString("1");
    }

    public function testRootException(): void
    {
        $fun1 = function () {
            throw new Exception("Error");
            yield;
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun1);
        try {
            $scheduler->then();
        } catch (Exception $exception) {
            $this->assertEquals("Error", $exception->getMessage());
        }
    }

    public function testRootReturnValue(): void
    {
        $fun1 = function () {
            yield;
            return 1;
        };
        $scheduler = new Scheduler();
        $scheduler->add($fun1);
        $result = $scheduler->then();
        $this->assertEquals([1], $result);
    }
}
