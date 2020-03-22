<?php

/**
 * 定义根目录常量
 */
define('BASE_PATH', realpath(__DIR__ . "/../"));

/**
 * AutoLoader
 */
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * App 入口
 */
require_once __DIR__ . "/../App/init.php";
