<?php

namespace App\Facades;

/**
 * @method static bool exists(string $path)
 * @method static false|int size(string $path)
 * @method static false|string get(string $path, bool $lock = false)
 * @method static false|string hash(string $path)
 * @method static false|int put(string $path, $contents, bool $lock = false)
 * @method static false|int append(string $path, $contents)
 * @method static false|int prepend(string $path, $contents)
 * @method static bool|false|string chmod(string $path, $mode = null)
 * @method static bool move(string $old_path, string $new_path)
 * @method static bool delete($paths)
 * @method static bool copy(string $source, string $dist)
 * @method static bool link(string $target, string $link)
 * @method static string|string[] name(string $path)
 * @method static string|string[] basename(string $path)
 * @method static string|string[] dirname(string $path)
 * @method static string|string[] extension(string $path)
 * @method static false|string type(string $path)
 * @method static string mimeType(string $path)
 * @method static false|int lastModified(string $path)
 * @method static bool isDirectory(string $path)
 * @method static bool isReadable(string $path)
 * @method static bool isWritable(string $path)
 * @method static bool isFile(string $path)
 * @method static array files(string $path)
 * @method static array allFiles(string $path)
 * @method static array directories(string $path)
 * @method static array allDirectories(string $path)
 * @method static bool makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false)
 * @method static bool deleteDirectory(string $path, bool $preserve = false)
 * @method static bool cleanDirectory(string $path)
 * @method static bool moveDirectory(string $source, string $target, bool $overwrite = false)
 * @method static bool copyDirectory(string $source, string $target)
 * @method static void vaildPath($path)
 *
 * @see \App\Utils\Storage
 */
class Storage extends Facade
{
    protected static $class = \App\Utils\Storage::class;
}
