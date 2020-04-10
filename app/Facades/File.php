<?php

namespace App\Facades;

/**
 * Class File
 * @package App\Facades
 *
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
class File extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Utils\File::class;
    }
}
