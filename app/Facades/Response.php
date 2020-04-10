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
     * @return \App\Kernel\Http\Response
     */
    public static function text(
        string $text,
        int $status = 200,
        array $headers = []
    ): \App\Kernel\Http\Response {
        return \App\Kernel\Http\Response::text($text, $status, $headers);
    }

    /**
     * @param string $html
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function html(
        string $html,
        int $status = 200,
        array $headers = []
    ): \App\Kernel\Http\Response {
        return \App\Kernel\Http\Response::html($html, $status, $headers);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return Response
     */
    public static function json(
        $data,
        int $status = 200,
        array $headers = [],
        int $options = 0
    ): \App\Kernel\Http\Response {
        return \App\Kernel\Http\Response::json(
            $data,
            $status,
            $headers,
            $options
        );
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function redirect(
        string $url,
        int $status = 302,
        array $headers = []
    ): \App\Kernel\Http\Response {
        return \App\Kernel\Http\Response::redirect($url, $status, $headers);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function make(
        $content = '',
        int $status = 200,
        array $headers = []
    ): \App\Kernel\Http\Response {
        return \App\Kernel\Http\Response::make($content, $status, $headers);
    }
}
