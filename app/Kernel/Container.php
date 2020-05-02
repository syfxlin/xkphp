<?php

namespace App\Kernel;

use App\Annotations\Autowired\Autowired;
use App\Exceptions\Kernel\BindFailException;
use App\Exceptions\Kernel\NotInstantiableException;
use App\Facades\Annotation;
use App\Http\Request;
use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use RuntimeException;
use ReflectionParameter;
use function apcu_exists;
use function apcu_fetch;
use function apcu_store;
use function array_merge;
use function array_pad;
use function class_exists;
use function compact;
use function config;
use function count;
use function explode;
use function get_class;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;
use function preg_match;
use function preg_split;
use function str_parse_callback;
use function strpos;

/**
 * IoC 容器，兼容 PSR-11
 */
class Container implements ContainerInterface
{
    /**
     * 容器中存储依赖的数组
     * 存储的是闭包，运行闭包会返回对应的依赖实例
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * 绑定方法
     *
     * @var array
     */
    protected $methodBindings = [];

    /**
     * 已创建的单例实例
     *
     * @var array
     */
    protected $instances = [];

    /**
     * 自动通过类名绑定类
     *
     * @var bool
     */
    protected $autobind = true;

    /**
     * 依赖别名
     *
     * @var string[]
     */
    protected $aliases = [];

    /**
     * 绑定依赖
     *
     * @param string|array $abstract 依赖名或者依赖列表
     * @param Closure|string|null $concrete 依赖闭包
     *
     * @param bool $shared
     * @param bool|string $alias
     * @param bool $overwrite
     * @return  Container
     */
    public function bind(
        $abstract,
        $concrete = null,
        bool $shared = false,
        $alias = false,
        bool $overwrite = false
    ): Container {
        // 同时绑定多个依赖
        if (is_array($abstract)) {
            foreach ($abstract as $_abstract => $value) {
                if (is_int($_abstract)) {
                    $_abstract = $value;
                }
                $_concrete = null;
                $_shared = false;
                $_alias = false;
                $_overwrite = false;
                if (is_bool($value)) {
                    $_shared = $value;
                } elseif (is_array($value)) {
                    [$_concrete, $_shared, $_alias, $_overwrite] = array_pad(
                        $value,
                        3,
                        false
                    );
                }
                $this->bind(
                    $_abstract,
                    $_concrete === false ? null : $_concrete,
                    $_shared,
                    $_alias,
                    $_overwrite
                );
            }
            return $this;
        }
        [$abstract, $alias] = $this->getAbstractAndAliasByAlias(
            $abstract,
            $alias
        );
        // 为了方便绑定依赖，可以节省一个参数
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->setBinding($abstract, $concrete, $shared, $overwrite);
        if ($alias) {
            $this->alias($abstract, $alias);
        }
        // 返回 this 使其支持链式调用
        return $this;
    }

    // 设置 binding
    protected function setBinding(
        string $abstract,
        $concrete,
        bool $shared = false,
        bool $overwrite = false
    ): void {
        $abstract = $this->getAbstractByAlias($abstract);
        // 传入的默认是闭包，如果没有传入闭包则默认创建
        if (!$concrete instanceof Closure) {
            $concrete = function (Container $c, array $args = []) use (
                $concrete
            ) {
                return $c->build($concrete, $args);
            };
        }
        // 判断是否是单例，是否被设置过
        if (!$overwrite && $shared && isset($this->bindings[$abstract])) {
            throw new BindFailException(
                "Target [$abstract] is a singleton and has been bind"
            );
        }
        // 设置绑定的闭包
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    // 获取 binding
    protected function getBinding(string $abstract)
    {
        $abstract = $this->getAbstractByAlias($abstract);
        if (!isset($this->bindings[$abstract])) {
            // 尝试自动绑定
            if (
                $this->autobind &&
                $abstract[0] !== '$' &&
                class_exists($abstract)
            ) {
                $this->setBinding($abstract, $abstract);
            } else {
                throw new BindFailException(
                    "Target [$abstract] is not binding or fail autobind"
                );
            }
        }
        return $this->bindings[$abstract];
    }

    // 判断 binding 是否存在
    protected function hasBinding(string $abstract): bool
    {
        $abstract = $this->getAbstractByAlias($abstract);
        return isset($this->bindings[$abstract]);
    }

    /**
     * 实例化对象
     *
     * @param string $abstract 对象名称
     * @param array $args
     *
     * @return  mixed
     */
    public function make(string $abstract, array $args = [])
    {
        $abstract = $this->getAbstractByAlias($abstract);
        $binding = $this->getBinding($abstract);
        $concrete = $binding['concrete'];
        $shared = $binding['shared'];
        // 判断是否是单例，若是单例并且已经实例化过就直接返回实例
        if ($shared && isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        // 构建实例
        $instance = $concrete($this, $args);
        // 判断是否是单例，若是则设置到容器的单例列表中
        if ($shared) {
            $this->instances[$abstract] = $instance;
        }
        return $instance;
    }

    /**
     * 绑定单例
     *
     * @param string $abstract 依赖名称
     * @param mixed $concrete 依赖闭包
     * @param bool|string $alias
     *
     * @param bool $overwrite
     * @return  Container
     */
    public function singleton(
        string $abstract,
        $concrete = null,
        $alias = false,
        bool $overwrite = false
    ): Container {
        $this->bind($abstract, $concrete, true, $alias, $overwrite);
        return $this;
    }

    /**
     * 绑定已实例化的单例
     *
     * @param string $abstract 依赖名称
     * @param mixed $instance 已实例化的单例
     * @param string|false $alias
     *
     * @param bool $overwrite
     * @return  Container
     */
    public function instance(
        string $abstract,
        $instance,
        $alias = false,
        bool $overwrite = false
    ): Container {
        [$abstract, $alias] = $this->getAbstractAndAliasByAlias(
            $abstract,
            $alias
        );
        $this->instances[$abstract] = $instance;
        $this->bind(
            $abstract,
            function () use ($instance) {
                return $instance;
            },
            true,
            $alias,
            $overwrite
        );
        return $this;
    }

    /**
     * 构建实例
     *
     * @param Closure|string $class 类名或者闭包
     * @param array $args
     * @return  mixed
     *
     * @throws ReflectionException
     */
    public function build($class, array $args = [])
    {
        if ($class instanceof Closure) {
            return $class($this, $args);
        }
        if (!class_exists($class)) {
            return $class;
        }
        // 取得反射类
        $reflector = new ReflectionClass($class);
        // 检查类是否可实例化
        if (!$reflector->isInstantiable()) {
            // 如果不能，意味着接口不能正常工作，报错
            throw new NotInstantiableException(
                "Target [$class] is not instantiable"
            );
        }
        // 取得构造函数
        $constructor = $reflector->getConstructor();
        // 检查是否有构造函数
        if ($constructor === null) {
            // 如果没有，就说明没有依赖，直接实例化
            $instance = new $class();
        } else {
            // 返回已注入依赖的参数数组
            $dependency = $this->injectingDependencies($constructor, $args);
            // 利用注入后的参数创建实例
            $instance = $reflector->newInstanceArgs($dependency);
        }
        $this->injectingProperties($reflector, $instance);
        return $instance;
    }

    /**
     * 注入依赖
     *
     * @param ReflectionFunction|ReflectionMethod $method
     * @param array $args
     *
     * @return  array
     */
    protected function injectingDependencies($method, array $args = []): array
    {
        $dependency = [];
        $parameters = $method->getParameters();
        $annotations = $this->getAnnotations($method);
        foreach ($parameters as $parameter) {
            if (isset($args[$parameter->name])) {
                $dependency[] = $args[$parameter->name];
                continue;
            }
            // 利用参数的类型声明，获取到参数的类型，然后从 bindings 中获取依赖注入
            $dependencyClass = $parameter->getClass();
            if ($dependencyClass === null) {
                $dependency[] = $this->resolvePrimitive(
                    $parameter,
                    $annotations
                );
            } else {
                // 实例化依赖
                $dependency[] = $this->resolveClass($parameter);
            }
        }
        return $dependency;
    }

    /**
     * 处理非类的依赖
     *
     * @param ReflectionParameter $parameter
     * @param array $annotations
     *
     * @return  mixed
     */
    protected function resolvePrimitive(
        ReflectionParameter $parameter,
        array $annotations
    ) {
        $abstract = $parameter->name;
        // 通过 bind 获取
        if ($this->hasBinding('$' . $parameter->name)) {
            $abstract = '$' . $parameter->name;
        }
        // 通过注解获取
        if (isset($annotations[$parameter->name])) {
            $abstract = $annotations[$parameter->name];
        }
        // 匹配别名
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAbstractByAlias($abstract);
        }
        try {
            $concrete = $this->getBinding($abstract)['concrete'];
        } catch (RuntimeException $e) {
            $concrete = null;
        }
        if ($concrete !== null) {
            return $concrete instanceof Closure ? $concrete($this) : $concrete;
        }
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        throw new BindFailException(
            "Target [$$parameter->name] is not binding"
        );
    }

    /**
     * 处理类依赖
     *
     * @param ReflectionParameter $parameter
     *
     * @return  mixed
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->make($parameter->getClass()->name);
        } catch (RuntimeException $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw $e;
        }
    }

    protected function injectingProperties(
        ReflectionClass $reflector,
        $instance
    ): void {
        $config = config('annotation');
        if (empty($config['autowired'])) {
            return;
        }
        $props = $reflector->getProperties();
        foreach ($props as $prop) {
            if ($prop->getDocComment() === false) {
                continue;
            }
            $anno = Annotation::getProperty($prop, Autowired::class);
            if ($anno !== null) {
                if (class_exists($anno->value)) {
                    $concrete = $this->make($anno->value);
                } else {
                    $abstract = $anno->value;
                    // 通过 bind 获取
                    if ($this->hasBinding('$' . $anno->value)) {
                        $abstract = '$' . $anno->value;
                    }
                    // 匹配别名
                    if ($this->isAlias($abstract)) {
                        $abstract = $this->getAbstractByAlias($abstract);
                    }
                    try {
                        $concrete = $this->getBinding($abstract)['concrete'];
                    } catch (RuntimeException $e) {
                        $concrete = null;
                    }
                }
                if ($concrete !== null) {
                    $prop->setValue(
                        $instance,
                        $concrete instanceof Closure
                            ? $concrete($this)
                            : $concrete
                    );
                }
            }
        }
    }

    /**
     * 设置自动绑定
     *
     * @param   bool  $use  是否自动绑定类
     *
     * @return  void
     */
    public function useAutoBind(bool $use): void
    {
        $this->autobind = $use;
    }

    /**
     * 判断是否绑定了指定的依赖
     *
     * @param $id
     * @return  bool
     */
    public function has($id): bool
    {
        return $this->hasBinding($id);
    }

    /**
     * 同 make
     *
     * @param   string  $id  对象名称
     *
     * @return  mixed
     */
    public function get($id)
    {
        return $this->make($id);
    }

    public function hasMethod(string $method): bool
    {
        return isset($this->methodBindings[$method]);
    }

    public function bindMethod(string $method, $callback): void
    {
        $this->methodBindings[$method] = $callback;
    }

    protected function getMethodBind(string $method)
    {
        if (isset($this->methodBindings[$method])) {
            return $this->methodBindings[$method];
        }
        throw new BindFailException("Target [$method] is not binding");
    }

    public function call(
        $method,
        array $args = [],
        $object = null,
        $isStatic = false
    ) {
        if ($object !== null) {
            return $this->callMethod($object, $method, $isStatic, $args);
        }
        if (
            is_array($method) ||
            (is_string($method) && preg_match('/@|::/', $method) > 0)
        ) {
            return $this->callClass($method, $args);
        }
        if (is_string($method)) {
            $method = $this->getMethodBind($method);
        }
        return $this->callFunction($method, $args);
    }

    protected function callFunction($method, array $args = [])
    {
        $reflector = new ReflectionFunction($method);
        $dependency = $this->injectingDependencies($reflector, $args);
        return $reflector->invokeArgs($dependency);
    }

    /**
     * @param string|array $target
     * @param array $args
     * @return mixed
     */
    protected function callClass($target, array $args = [])
    {
        $class = null;
        $method = null;
        $object = null;
        $isStatic = false;
        if (is_string($target)) {
            $isStatic = strpos($target, '@') === false;
            [$class, $method] = str_parse_callback($target);
            $object = $this->bindAndMakeReflection($class, $isStatic);
        } else {
            if (count($target) === 3) {
                [$class, $split, $method] = $target;
                $isStatic = $split === '::';
            } else {
                [$class, $method] = $target;
            }
            $object = $this->bindAndMakeReflection($class, $isStatic);
        }
        return $this->callMethod($object, $method, $isStatic, $args);
    }

    protected function bindAndMakeReflection(
        string $class,
        bool $isStatic = false
    ) {
        if ($isStatic) {
            return $class;
        }
        if (!$this->has($class)) {
            $this->bind($class);
        }
        return $this->make($class);
    }

    protected function callMethod(
        $object,
        $method,
        $isStatic = false,
        array $args = []
    ) {
        $reflector = new ReflectionMethod($object, $method);
        $dependency = $this->injectingDependencies($reflector, $args);
        return $reflector->invokeArgs($isStatic ? null : $object, $dependency);
    }

    public function callWithRequest(
        Request $request,
        $method,
        array $args = [],
        $object = null,
        $isStatic = false
    ) {
        $args = array_merge(
            $request->getUploadedFiles(),
            $request->getCookieParams(),
            $request->getAttributes(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $args
        );
        return $this->call($method, $args, $object, $isStatic);
    }

    public function isAlias(string $name): bool
    {
        return isset($this->aliases[$name]);
    }

    public function alias(string $abstract, string $alias): void
    {
        if ($abstract === $alias) {
            return;
        }
        $this->aliases[$alias] = $abstract;
    }

    public function getAlias($abstract)
    {
        foreach ($this->aliases as $alias => $value) {
            if ($value === $abstract) {
                return $alias;
            }
        }
        return $abstract;
    }

    public function removeAlias($alias): void
    {
        unset($this->aliases[$alias]);
    }

    protected function getAbstractByAlias($alias)
    {
        return $this->aliases[$alias] ?? $alias;
    }

    protected function getAbstractAndAliasByAlias(
        $alias,
        $inAlias = false
    ): array {
        $abstract = $this->getAbstractByAlias($alias);
        if ($alias === $abstract) {
            return [$abstract, $inAlias];
        }
        if (!$inAlias) {
            return [$abstract, $alias];
        }
        return [$abstract, $inAlias];
    }

    protected function getAnnotations($method): array
    {
        // 如果不是方法或者关闭了DI就直接返回空
        $config = config('annotation');
        if (
            !$method instanceof ReflectionMethod ||
            empty($config['di']) ||
            $method->getDocComment() === false
        ) {
            return [];
        }
        $props = Annotation::getList($method, 'App\Annotations\DI');
        $result = [];
        foreach ($props as $prop) {
            $result[$prop->name] = $prop->value;
        }
        return $result;
    }

    public function callWithAspect(
        $target,
        array $args = [],
        $object = null,
        $isStatic = false,
        array $aspects = []
    ) {
        $class_method = ['', ''];
        if ($object !== null) {
            $class_method[0] = get_class($object);
        }
        if (is_string($target)) {
            if (preg_match('/@|::/', $target) > 0) {
                $class_method = preg_split('/@|::/', $target);
            } else {
                $target = $this->getMethodBind($target);
            }
        } elseif (is_array($target)) {
            $class_method = $target;
            $target = "{$target[0]}@{$target[1]}";
        }
        return AspectManager::weavingAspectWithClosure(
            function () use ($target, $args, $object, $isStatic) {
                $this->call($target, $args, $object, $isStatic);
            },
            $class_method,
            $args,
            $aspects
        );
    }
}
