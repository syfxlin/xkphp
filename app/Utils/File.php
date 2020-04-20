<?php

namespace App\Utils;

use RuntimeException;
use function array_filter;
use function array_push;
use function array_values;
use function chmod;
use function clearstatcache;
use function copy;
use function fclose;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function fileperms;
use function filesize;
use function filetype;
use function flock;
use function fopen;
use function fread;
use function is_array;
use function is_dir;
use function is_file;
use function is_readable;
use function is_writable;
use function link;
use function md5_file;
use function mime_content_type;
use function mkdir;
use function pathinfo;
use function rename;
use function rmdir;
use function scandir;
use function sprintf;
use function substr;
use function unlink;

/**
 * 文件操作类，具体使用方式参考 Laravel
 */
class File
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function size(string $path)
    {
        return filesize($path);
    }

    public function get(string $path, bool $lock = false)
    {
        if (!$this->exists($path)) {
            throw new RuntimeException("File does not exist at path $path");
        }
        if (!$lock) {
            return file_get_contents($path);
        }
        $contents = '';
        $handle = fopen($path, 'rb');
        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);
                    $contents = fread($handle, $this->size($path) ?: 1);
                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }
        return $contents;
    }

    public function hash(string $path): string
    {
        return md5_file($path);
    }

    public function put(string $path, $contents, bool $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    public function append(string $path, $contents)
    {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    public function prepend(string $path, $contents)
    {
        if (!$this->exists($path)) {
            return $this->put($path, $contents);
        }
        return $this->put($path, $contents . $this->get($path));
    }

    public function chmod(string $path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        return substr(sprintf('%o', fileperms($path)), -4);
    }

    public function move(string $old_path, string $new_path): bool
    {
        return rename($old_path, $new_path);
    }

    public function delete($paths): bool
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        $flag = true;
        foreach ($paths as $path) {
            $flag = $flag ? @unlink($path) : false;
        }
        return $flag;
    }

    public function copy(string $source, string $dist): bool
    {
        return copy($source, $dist);
    }

    public function link(string $target, string $link): bool
    {
        return link($target, $link);
    }

    public function name(string $path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function basename(string $path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    public function dirname(string $path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    public function extension(string $path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function type(string $path): string
    {
        return filetype($path);
    }

    public function mimeType(string $path): string
    {
        return mime_content_type($path);
    }

    public function lastModified(string $path)
    {
        return filemtime($path);
    }

    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    public function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function files(string $path): array
    {
        return array_values(
            array_filter(scandir($path), function ($item) use ($path) {
                return !is_dir("$path/$item");
            })
        );
    }

    private function walkAllFiles(string $path, $root = null): array
    {
        $files = [];
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item_path = "$path/$item";
            $item = $root === null ? $item : "$root/$item";
            if (is_file($item_path)) {
                $files[] = $item;
            } else {
                array_push($files, ...$this->walkAllFiles($item_path, $item));
            }
        }
        return $files;
    }

    public function allFiles(string $path): array
    {
        return $this->walkAllFiles($path);
    }

    public function directories(string $path): array
    {
        return array_values(
            array_filter(scandir($path), function ($item) use ($path) {
                return is_dir("$path/$item") && $item !== '.' && $item !== '..';
            })
        );
    }

    private function walkAllDirs(string $path, $root = null): array
    {
        $dirs = [];
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item_path = "$path/$item";
            $item = $root === null ? $item : "$root/$item";
            if (is_dir($item_path)) {
                $dirs[] = $item;
                array_push($dirs, ...$this->walkAllDirs($item_path, $item));
            }
        }
        return $dirs;
    }

    public function allDirectories(string $path): array
    {
        return $this->walkAllDirs($path);
    }

    public function makeDirectory(
        string $path,
        int $mode = 0755,
        bool $recursive = false,
        bool $force = false
    ): bool {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    public function deleteDirectory(string $path, bool $preserve = false): bool
    {
        if (!is_dir($path)) {
            return false;
        }
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_dir("$path/$item")) {
                $this->deleteDirectory("$path/$item", $preserve);
            } else {
                @unlink("$path/$item");
            }
        }
        if (!$preserve) {
            @rmdir($path);
        }
        return true;
    }

    public function cleanDirectory(string $path): bool
    {
        return $this->deleteDirectory($path, true);
    }

    public function moveDirectory(
        string $source,
        string $target,
        bool $overwrite = false
    ): bool {
        if ($overwrite && is_dir($target)) {
            $this->deleteDirectory($target);
        }
        return @rename($source, $target);
    }

    public function copyDirectory(string $source, string $target): bool
    {
        if (!is_dir($source)) {
            return false;
        }
        if (!is_dir($target)) {
            $this->makeDirectory($target, 0777, true);
        }
        foreach (scandir($source) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_dir("$source/$item")) {
                $this->copyDirectory("$source/$item", "$target/$item");
            } else {
                copy("$source/$item", "$target/$item");
            }
        }
        return true;
    }
}
