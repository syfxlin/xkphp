<?php

namespace App\Kernel;

use App\Facades\App;
use App\Facades\Auth;
use App\Http\Request;
use RuntimeException;
use function array_pop;
use function array_reduce;
use function array_unshift;
use function asset;
use function csrf_token;
use function getDotData;
use function htmlspecialchars;
use function json_encode;
use function ob_get_clean;
use function ob_start;
use function request;
use function view_path;

/**
 * @method void include(string $view)
 * @method void echo(string $content)
 * @method void extends(string $view)
 * @method void yield(string $name)
 */
class ViewHtml
{
    /**
     * @var array
     */
    public static $data = [];

    /**
     * 继承至其他视图
     *
     * @var string|null
     */
    public static $extends = null;

    /**
     * 继承父级视图的填充数据
     *
     * @var array
     */
    protected static $section = [];

    /**
     * @var null|string
     */
    protected static $section_name = null;

    /**
     * @var array
     */
    protected static $stack = [];

    /**
     * @var null|string
     */
    protected static $stack_name = null;

    public function data(string $key = null)
    {
        if ($key === null) {
            return self::$data;
        }
        return getDotData($key, self::$data);
    }

    public function csrfToken()
    {
        return csrf_token();
    }

    public function csrfMetaTag(): void
    {
        $csrf_token = csrf_token();
        echo "<meta name=\"csrf-token\" content=\"$csrf_token\">";
    }

    public function request(): Request
    {
        return request();
    }

    public function auth(): bool
    {
        return Auth::check();
    }

    public function guest(): bool
    {
        return Auth::guest();
    }

    public function csrf(): void
    {
        $csrf_token = csrf_token();
        echo "<input type=\"hidden\" name=\"_token\" value=\"$csrf_token\">";
    }

    public function _include(string $view): void
    {
        include view_path($view);
    }

    public function _echo(string $content): void
    {
        echo htmlspecialchars($content);
    }

    public function json($data, int $option = 0, int $depth = 512): void
    {
        echo json_encode($data, $option, $depth);
    }

    public function asset(string $asset): void
    {
        echo asset($asset);
    }

    public function _extends(string $view): void
    {
        self::$extends = $view;
    }

    public function section(string $name, $data = null): void
    {
        if ($data !== null) {
            self::$section[$name] = $data;
        } else {
            ob_start();
            self::$section_name = $name;
        }
    }

    public function endsection(): void
    {
        if (self::$section_name === null) {
            throw new RuntimeException(
                'Endsection does not have a corresponding start section.'
            );
        }
        self::$section[self::$section_name] = ob_get_clean();
        self::$stack_name = null;
    }

    public function _yield(string $name): void
    {
        echo self::$section[$name];
    }

    public function error(string $name)
    {
        return self::$data['errors'][$name] ?? false;
    }

    public function stack(string $name): void
    {
        foreach (self::$stack[$name] ?? [] as $item) {
            echo $item;
        }
    }

    public function push(string $name): void
    {
        if (!isset(self::$stack[$name])) {
            self::$stack[$name] = [];
        }
        ob_start();
        self::$stack_name = $name;
    }

    public function endpush(): void
    {
        if (self::$stack_name === null) {
            throw new RuntimeException(
                'Endpush does not have a corresponding start section.'
            );
        }
        self::$stack[self::$stack_name][] = ob_get_clean();
        self::$stack_name = null;
    }

    public function prepend(string $name): void
    {
        $this->push($name);
    }

    public function endprepend(): void
    {
        $name = self::$stack_name;
        $this->endpush();
        array_unshift(self::$stack[$name], array_pop(self::$stack[$name]));
    }

    public function inject(string $abstract)
    {
        return App::make($abstract);
    }

    public function __call($name, $arguments)
    {
        $name = '_' . $name;
        return $this->$name(...$arguments);
    }
}
