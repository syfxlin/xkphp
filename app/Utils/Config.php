<?php

namespace App\Utils;

use function config_path;
use function dget;
use function dirname;
use function dset;
use function glob;
use function is_array;
use function pathinfo;

class Config
{
    /**
     * 配置项，动态读取
     *
     * @var array
     */
    private static $config;

    public function __construct()
    {
        foreach (glob(config_path() . '/*.php') as $path) {
            self::$config[pathinfo($path, PATHINFO_FILENAME)] = require $path;
        }
    }

    /**
     * 获取配置文件地址
     *
     * @param   string  $config_name  配置文件名称
     *
     * @return  string                配置文件地址
     */
    public function path(string $config_name): string
    {
        return dirname(__DIR__, 2) . "/config/$config_name.php";
    }

    /**
     * 获取所有配置
     *
     * @return array   所有配置
     */
    public function all(): array
    {
        return self::$config;
    }

    /**
     * 获取指定配置
     *
     * @param   string  $name     配置名称，可以使用 ”.“ 来读取子配置
     * @param   mixed   $default  默认配置，如果设置到指定的配置就用该值代替
     *
     * @return  mixed             配置值
     */
    public function get(string $name, $default = null)
    {
        return dget(self::$config, $name, $default);
    }

    private function setItem(string $name, $value): void
    {
        dset(self::$config, $name, $value);
    }

    /**
     * 设置指定的配置
     *
     * @param   mixed   $name   配置名称，可以使用 "." 设置子配置，若传入 array 则可以设置多个值
     * @param   mixed   $value  配置的值
     *
     * @return  void
     */
    public function set($name, $value = null): void
    {
        if (is_array($name)) {
            foreach ($name as $key => $v) {
                $this->setItem($key, $v);
            }
        } else {
            $this->setItem($name, $value);
        }
    }

    /**
     * 判断配置是否存在
     *
     * @param   string  $name  配置名称
     *
     * @return  bool           是否存在
     */
    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    /**
     * 和 set 方法一致，只不过只能设置一个
     *
     * @param   string  $name   配置名称
     * @param   mixed   $value  配置的值
     *
     * @return  void
     */
    public function push(string $name, $value): void
    {
        $this->setItem($name, $value);
    }
}
