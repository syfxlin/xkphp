<?php

namespace App\Utils;

use RuntimeException;
use function in_array;
use function is_array;
use function realpath;
use function storage_path;
use function stripos;

/**
 * @method bool exists(string $path)
 * @method false|int size(string $path)
 * @method false|string get(string $path, bool $lock = false)
 * @method false|string hash(string $path)
 * @method false|int put(string $path, $contents, bool $lock = false)
 * @method false|int append(string $path, $contents)
 * @method false|int prepend(string $path, $contents)
 * @method bool|false|string chmod(string $path, $mode = null)
 * @method bool move(string $old_path, string $new_path)
 * @method bool delete($paths)
 * @method bool copy(string $source, string $dist)
 * @method bool link(string $target, string $link)
 * @method string|string[] name(string $path)
 * @method string|string[] basename(string $path)
 * @method string|string[] dirname(string $path)
 * @method string|string[] extension(string $path)
 * @method false|string type(string $path)
 * @method string mimeType(string $path)
 * @method false|int lastModified(string $path)
 * @method bool isDirectory(string $path)
 * @method bool isReadable(string $path)
 * @method bool isWritable(string $path)
 * @method bool isFile(string $path)
 * @method array files(string $path)
 * @method array allFiles(string $path)
 * @method array directories(string $path)
 * @method array allDirectories(string $path)
 * @method bool makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false)
 * @method bool deleteDirectory(string $path, bool $preserve = false)
 * @method bool cleanDirectory(string $path)
 * @method bool moveDirectory(string $source, string $target, bool $overwrite = false)
 * @method bool copyDirectory(string $source, string $target)
 *
 * @see \App\Utils\File
 */
class Storage
{
    /**
     * @param string|array $path
     */
    public function vaildPath($path): void
    {
        $path = is_array($path) ? $path : [$path];
        foreach ($path as $value) {
            if (stripos(realpath($value), storage_path()) !== 0) {
                throw new RuntimeException(
                    'Path is outside of the defined root'
                );
            }
        }
    }

    public function __call($name, $arguments)
    {
        $two_args_fun = [
            'move',
            'copy',
            'link',
            'moveDirectory',
            'copyDirectory'
        ];
        $arguments[0] = realpath(storage_path() . '/' . $arguments[0]);
        $this->vaildPath($arguments[0]);
        if (in_array($name, $two_args_fun, true)) {
            $arguments[1] = realpath(storage_path() . '/' . $arguments[1]);
            $this->vaildPath($arguments[1]);
        }
        $file = new File();
        return $file->$name(...$arguments);
    }
}
