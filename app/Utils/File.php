<?php

namespace App\Utils;

class File
{
    public function exists($path)
    {
        return file_exists($path);
    }

    public function size($path)
    {
        return filesize($path);
    }

    public function get($path, $lock = false)
    {
        if (!$this->exists($path)) {
            throw new \RuntimeException("File does not exist at path $path");
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

    public function hash($path)
    {
        return md5_file($path);
    }

    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    public function append($path, $contents)
    {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    public function prepend($path, $contents)
    {
        if (!$this->exists($path)) {
            return $this->put($path, $contents);
        }
        return $this->put($path, $contents . $this->get($path));
    }

    public function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        return substr(sprintf('%o', fileperms($path)), -4);
    }

    public function move($old_path, $new_path)
    {
        return rename($old_path, $new_path);
    }

    public function delete($paths)
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

    public function copy($source, $dist)
    {
        return copy($source, $dist);
    }

    public function link($target, $link)
    {
        return link($target, $link);
    }

    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function type($path)
    {
        return filetype($path);
    }

    public function mimeType($path)
    {
        return mime_content_type($path);
    }

    public function lastModified($path)
    {
        return filemtime($path);
    }

    public function isDirectory($path)
    {
        return is_dir($path);
    }

    public function isReadable($path)
    {
        return is_readable($path);
    }

    public function isWritable($path)
    {
        return is_writable($path);
    }

    public function isFile($path)
    {
        return is_file($path);
    }

    public function files($path)
    {
        return array_values(array_filter(scandir($path), function ($item) use ($path) {
            return !is_dir("$path/$item");
        }));
    }

    private function walkAllFiles($path, $root = null)
    {
        $files = [];
        foreach (scandir($path) as $item) {
            if ($item === "." || $item === "..") {
                continue;
            }
            $item_path = "$path/$item";
            $item = $root === null ? $item : "$root/$item";
            if (is_file($item_path)) {
                $files[] = $item;
            } else {
                $files = array_merge($files, $this->walkAllFiles($item_path, $item));
            }
        }
        return $files;
    }

    public function allFiles($path)
    {
        return $this->walkAllFiles($path);
    }

    public function directories($path)
    {
        return array_values(array_filter(scandir($path), function ($item) use ($path) {
            return is_dir("$path/$item") && $item !== "." && $item !== "..";
        }));
    }

    private function walkAllDirs($path, $root = null)
    {
        $dirs = [];
        foreach (scandir($path) as $item) {
            if ($item === "." || $item === "..") {
                continue;
            }
            $item_path = "$path/$item";
            $item = $root === null ? $item : "$root/$item";
            if (is_dir($item_path)) {
                $dirs[] = $item;
                $dirs = array_merge($dirs, $this->walkAllDirs($item_path, $item));
            }
        }
        return $dirs;
    }

    public function allDirectories($path)
    {
        return $this->walkAllDirs($path);
    }

    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    public function deleteDirectory($path, $preserve = false)
    {
        if (!is_dir($path)) {
            return false;
        }
        foreach (scandir($path) as $item) {
            if ($item === "." || $item === "..") {
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

    public function cleanDirectory($path)
    {
        return $this->deleteDirectory($path, true);
    }

    public function moveDirectory($source, $target, $overwrite = false)
    {
        if ($overwrite && is_dir($target)) {
            $this->deleteDirectory($target);
        }
        return @rename($source, $target);
    }

    public function copyDirectory($source, $target)
    {
        if (!is_dir($source)) {
            return false;
        }
        if (!is_dir($target)) {
            $this->makeDirectory($target, 0777, true);
        }
        foreach (scandir($source) as $item) {
            if ($item === "." || $item === "..") {
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
