<?php

namespace App\Facades;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionMethod;
use function array_map;
use function array_push;

class Annotation
{
    public static function getList(
        ReflectionMethod $method,
        string $namespace
    ): array {
        $reader = App::make(AnnotationReader::class);
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
        $reader = App::make(AnnotationReader::class);
        return $reader->getMethodAnnotation($method, $annotation);
    }
}
