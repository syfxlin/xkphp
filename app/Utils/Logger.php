<?php

namespace App\Utils;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Throwable;
use function count;
use function date_create;
use function file_put_contents;
use function get_class;
use function implode;
use function in_array;
use function ini_set;
use function is_object;
use function str_replace;
use function strtolower;
use function strtoupper;
use function substr;
use function var_export;

class Logger implements LoggerInterface
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

        'bg_red' => '41;37',
    ];

    public const OPTION = [
        'bold' => '1',
        'underline' => '4',
        'flicker' => '5',
        'reverse' => '7',
        'hide' => '8',
    ];

    public const LOG_COLOR = [
        LogLevel::EMERGENCY => 'red',
        LogLevel::ALERT => 'red',
        LogLevel::CRITICAL => 'red',
        LogLevel::ERROR => 'red',
        LogLevel::WARNING => 'yellow',
        LogLevel::NOTICE => 'cyan',
        LogLevel::INFO => 'cyan',
        LogLevel::DEBUG => 'magenta',
    ];

    public const LOG_OPTION = [
        LogLevel::EMERGENCY => 'reverse',
        LogLevel::ALERT => 'reverse',
        LogLevel::CRITICAL => 'reverse',
    ];

    /**
     * @var string
     */
    protected $print_to = 'console';

    /**
     * @var string
     */
    public $log_name = 'XK-Log';

    public function __construct($options = [])
    {
        ini_set('date.timezone', $options['timezone'] ?? 'Asia/Shanghai');
        $this->print_to = $options['print_to'] ?? 'console';
        $this->log_name = $options['log_name'] ?? 'XK-Log';
    }

    protected function render($text, $color, $option = null): string
    {
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

    protected function send($text): void
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

    public function parseError(Throwable $e): string
    {
        $code = $e->getCode();
        $class = get_class($e);
        $message = $e->getMessage() === '' ? 'null' : $e->getMessage();
        $stack = $this->stack($e);
        return $this->render(
            "\n Message: $message, Code: $code, Class: $class",
            'red'
        ) . "\n$stack";
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

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $level = strtolower($level);
        $error = null;
        if (isset($context['error'])) {
            $error = $context['error'];
            unset($context['error']);
        }
        if (
            !in_array($level, [
                'emergency',
                'alert',
                'critical',
                'error',
                'warning',
                'notice',
                'info',
                'debug',
            ])
        ) {
            throw new RuntimeException(
                "The error level of the log is set incorrectly"
            );
        }
        $time = $this->time();
        $title = strtoupper($level);
        $out = "[$this->log_name] $title: $message | $time";
        $out .= self::END;
        $out .= $this->parse($context);
        if ($error !== null) {
            $out .= $this->parseError($error);
        }
        $this->send(
            $this->render(
                $out,
                self::LOG_COLOR[$level],
                self::LOG_OPTION[$level] ?? null
            )
        );
    }
}
