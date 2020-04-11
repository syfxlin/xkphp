<?php

namespace App\Facades;

/**
 * Class Response
 * @package App\Facades
 */
class Response
{
    /**
     * @param string $text
     * @param int $status
     * @param array $headers
     * @return \App\Http\Response
     */
    public static function text(
        string $text,
        int $status = 200,
        array $headers = []
    ): \App\Http\Response {
        return \App\Http\Response::text($text, $status, $headers);
    }

    /**
     * @param string $html
     * @param int $status
     * @param array $headers
     * @return \App\Http\Response
     */
    public static function html(
        string $html,
        int $status = 200,
        array $headers = []
    ): \App\Http\Response {
        return \App\Http\Response::html($html, $status, $headers);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \App\Http\Response
     */
    public static function json(
        $data,
        int $status = 200,
        array $headers = [],
        int $options = 0
    ): \App\Http\Response {
        return \App\Http\Response::json($data, $status, $headers, $options);
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return \App\Http\Response
     */
    public static function redirect(
        string $url,
        int $status = 302,
        array $headers = []
    ): \App\Http\Response {
        return \App\Http\Response::redirect($url, $status, $headers);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return \App\Http\Response
     */
    public static function make(
        $content = '',
        int $status = 200,
        array $headers = []
    ): \App\Http\Response {
        return \App\Http\Response::make($content, $status, $headers);
    }
}
