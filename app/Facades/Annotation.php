<?php

namespace App\Facades;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionMethod;
use ReflectionProperty;
use function array_push;

class Annotation
{
    /**
     * @var AnnotationReader
     */
    public static $reader;

    public static function getInstance(): AnnotationReader
    {
        if (isset(self::$reader)) {
            return self::$reader;
        }
        return self::$reader = new AnnotationReader();
    }

    public static function getList(
        ReflectionMethod $method,
        string $namespace
    ): array {
        // Read Cache
        $cache_enable = APCu::isEnable();
        $cache_name = "anno_m_$method->class@$method->name@$namespace";
        if ($cache_enable && APCu::exists($cache_name)) {
            return APCu::fetch($cache_name);
        }

        $reader = self::getInstance();
        $props = [];
        $anno = $reader->getMethodAnnotation($method, "$namespace\Item");
        if ($anno !== null) {
            $props[] = $anno;
        }
        $anno = $reader->getMethodAnnotation($method, "$namespace\Set");
        if ($anno !== null) {
            array_push($props, ...$anno->values);
        }

        // Set Cache
        if ($cache_enable) {
            APCu::store($cache_name, $props);
        }

        return $props;
    }

    public static function get(ReflectionMethod $method, string $annotation)
    {
        // Read Cache
        $cache_enable = APCu::isEnable();
        $cache_name = "anno_m_$method->class@$method->name@$annotation";
        if ($cache_enable && APCu::exists($cache_name)) {
            return APCu::fetch($cache_name);
        }

        $reader = self::getInstance();
        $result = $reader->getMethodAnnotation($method, $annotation);

        // Set Cache
        if ($cache_enable) {
            APCu::store($cache_name, $result);
        }

        return $result;
    }

    public static function getProperty(
        ReflectionProperty $property,
        string $annotation
    ) {
        // Read Cache
        $cache_enable = APCu::isEnable();
        $cache_name = "anno_p_$property->class@$property->name@$annotation";
        if ($cache_enable && APCu::exists($cache_name)) {
            return APCu::fetch($cache_name);
        }

        $reader = self::getInstance();
        $result = $reader->getPropertyAnnotation($property, $annotation);

        // Set Cache
        if ($cache_enable) {
            APCu::store($cache_name, $result);
        }

        return $result;
    }
}
