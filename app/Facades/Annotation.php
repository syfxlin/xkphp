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
        return $props;
    }

    public static function get(ReflectionMethod $method, string $annotation)
    {
        $reader = self::getInstance();
        return $reader->getMethodAnnotation($method, $annotation);
    }

    public static function getProperty(
        ReflectionProperty $property,
        string $annotation
    ) {
        $reader = self::getInstance();
        return $reader->getPropertyAnnotation($property, $annotation);
    }
}
