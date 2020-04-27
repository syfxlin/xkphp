<?php

namespace App\Utils;

use Throwable;
use function count;
use function date_create;
use function file_put_contents;
use function get_class;
use function implode;
use function ini_set;
use function is_object;
use function str_replace;
use function strtolower;
use function substr;
use function var_export;

class Console
{
    public const BEGIN = "\33[";
    public const MIDDLE = 'm';
    public const END = "\33[0m";

    public const COLOR = [
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'white' => '37',

        'bright_black' => '90',
        'bright_red' => '91',
        'bright_green' => '92',
        'bright_yellow' => '93',
        'bright_blue' => '94',
        'bright_magenta' => '95',
        'bright_cyan' => '96',
        'bright_white' => '97',

        'bg_red' => '41;37'
    ];

    public const OPTION = [
        'bold' => '1',
        'underline' => '4',
        'flicker' => '5',
        'reverse' => '7',
        'hide' => '8'
    ];

    protected $print_to = 'console';

    public function __construct($options = [])
    {
        ini_set('date.timezone', $options['timezone'] ?? 'Asia/Shanghai');
        $this->print_to = $options['print_to'] ?? 'console';
    }

    protected function render(
        string $text,
        string $color,
        string $option = null
    ): string {
        if ($this->print_to !== 'console') {
            return $text;
        }
        $color = self::COLOR[strtolower($color)] ?? $color;
        $option =
            $option === null
                ? ''
                : (self::OPTION[strtolower($option)] ?? $option) . ';';
        return self::BEGIN .
            $option .
            $color .
            self::MIDDLE .
            $text .
            self::END;
    }

    protected function send(string $text): void
    {
        if ($this->print_to === 'console') {
            file_put_contents('php://stdout', "$text\n");
        } else {
            file_put_contents($this->print_to, "$text\n", FILE_APPEND);
        }
    }

    protected function stack(Throwable $e): string
    {
        $trace = $e->getTrace();
        $stack = [];
        foreach ($trace as $index => $item) {
            $info = isset($item['class'])
                ? $this->render("{$item['class']}{$item['type']}", 'cyan')
                : '';
            $info .= $this->render($item['function'], 'cyan');
            $file = isset($item['file'], $item['line'])
                ? $this->render(
                    "({$item['file']}:{$item['line']})",
                    'green',
                    'underline'
                )
                : '';
            $key = $this->render("#$index", 'magenta');
            $stack[] = " |- $key $info $file";
        }
        $key = $this->render('#' . count($trace), 'magenta');
        $info = $this->render('{main}', 'cyan');
        $stack[] = " |- $key $info";
        return implode("\n", $stack);
    }

    protected function time(): string
    {
        $date = date_create()->format('Y-m-d H:i:s.u');
        return substr($date, 0, -3);
    }

    public function error(Throwable $e): void
    {
        $time = $this->time();
        $code = $e->getCode();
        $class = get_class($e);
        $message = $e->getMessage() === '' ? 'null' : $e->getMessage();
        $stack = $this->stack($e);
        $out = "[XK-Log] ERROR[$class]($code): $message | $time";
        $this->send($this->render($out, 'red') . "\n$stack");
    }

    public function fatal(Throwable $e): void
    {
        $time = $this->time();
        $code = $e->getCode();
        $class = get_class($e);
        $message = $e->getMessage() === '' ? 'null' : $e->getMessage();
        $stack = $this->stack($e);
        $out = "[XK-Log] FATAL[$class]($code): $message | $time";
        $this->send($this->render($out, 'red', 'reverse') . "\n$stack");
    }

    protected function parse($objects): string
    {
        $out = '';
        foreach ($objects as $obj) {
            if (is_object($obj)) {
                $out .= "\n " . $this->render(get_class($obj) . ':', 'cyan');
            }
            $exp =
                "\n   |- " .
                str_replace("\n", "\n   |- ", var_export($obj, true));
            $out .= $this->render($exp, 'green');
        }
        return $out;
    }

    public function warn(
        string $title = 'null',
        string $message = 'null',
        ...$objects
    ): void {
        $time = $this->time();
        $out = "[XK-Log] WARN[$title]: $message | $time";
        $out .= $this->parse($objects);
        $this->send($this->render($out, 'yellow'));
    }

    public function info(
        string $title = 'null',
        string $message = 'null',
        ...$objects
    ): void {
        $time = $this->time();
        $out = "[XK-Log] INFO[$title]: $message | $time";
        $out .= $this->parse($objects);
        $this->send($this->render($out, 'cyan'));
    }

    public function debug(
        string $title = 'null',
        string $message = 'null',
        ...$objects
    ): void {
        $time = $this->time();
        $out = "[XK-Log] DEBUG[$title]: $message | $time";
        $out .= $this->parse($objects);
        $this->send($this->render($out, 'magenta'));
    }
}
