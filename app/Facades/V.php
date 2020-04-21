<?php

namespace App\Facades;

use App\Kernel\ViewHtml;

/**
 * Class V
 * @package App\Facades
 *
 * @method static mixed data(string $key = null)
 * @method static mixed csrfToken()
 * @method static void csrfMetaTag()
 * @method static Request request()
 * @method static bool auth()
 * @method static bool guest()
 * @method static void csrf()
 * @method static void include(string $view)
 * @method static void echo(string $content)
 * @method static void json($data, int $option = 0, int $depth = 512)
 * @method static void asset(string $asset)
 * @method static void extends(string $view)
 * @method static void section(string $name, $data = null)
 * @method static void endsection()
 * @method static void yield(string $name)
 * @method static bool error(string $name)
 * @method static void stack(string $name)
 * @method static void push(string $name)
 * @method static void endpush()
 * @method static void prepend(string $name)
 * @method static void endprepend()
 * @method static mixed inject(string $abstract)
 *
 * @see \App\Kernel\ViewHtml
 */
class V extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ViewHtml::class;
    }
}
