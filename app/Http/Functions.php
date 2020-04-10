<?php

namespace App\Http;

use Psr\Http\Message\UploadedFileInterface;

class Functions
{
    /**
     * @param array $tree
     * @return array
     */
    private static function resolveStructure(array $tree): array
    {
        $result = [];
        foreach ($tree['tmp_name'] as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::resolveStructure($value);
                continue;
            }
            $result[$key] = new UploadFile(
                $tree['tmp_name'][$key],
                $tree['size'][$key],
                $tree['error'][$key],
                $tree['name'][$key] ?? null,
                $tree['type'][$key] ?? null
            );
        }
        return $result;
    }

    /**
     * @param array $files
     * @return array
     */
    public static function convertFiles(array $files): array
    {
        $result = [];
        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $result[$key] = $value;
                continue;
            }
            if (
                is_array($value) &&
                isset($value['tmp_name']) &&
                is_array($value['tmp_name'])
            ) {
                $result[$key] = self::resolveStructure($value);
                continue;
            }
            if (is_array($value) && isset($value['tmp_name'])) {
                $result[$key] = new UploadFile(
                    $value['tmp_name'],
                    $value['size'],
                    $value['error'],
                    $value['name'],
                    $value['type']
                );
                continue;
            }
            if (is_array($value)) {
                $result[$key] = self::convertFiles($value);
                continue;
            }
        }
        return $result;
    }

    /**
     * @param array $server
     * @return array
     */
    public static function parseHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if ($value === '') {
                continue;
            }
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
                continue;
            }
        }
        return $headers;
    }
}
