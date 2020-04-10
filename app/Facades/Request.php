<?php

namespace App\Facades;

use App\Kernel\Http\UploadFile;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package App\Facades
 *
 * @method static string getProtocolVersion()
 * @method static \App\Kernel\Http\Request withProtocolVersion($version)
 * @method static array getHeaders()
 * @method static bool hasHeader($name)
 * @method static array getHeader($name)
 * @method static string getHeaderLine($name)
 * @method static \App\Kernel\Http\Request withHeader($name, $value)
 * @method static \App\Kernel\Http\Request withAddedHeader($name, $value)
 * @method static \App\Kernel\Http\Request withoutHeader($name)
 * @method static \App\Kernel\Http\Request withHeaders(array $headers)
 * @method static StreamInterface getBody()
 * @method static \App\Kernel\Http\Request withBody(StreamInterface $body)
 * @method static string getRequestTarget()
 * @method static \App\Kernel\Http\Request withRequestTarget($request_target)
 * @method static string getMethod()
 * @method static \App\Kernel\Http\Request withMethod($method)
 * @method static UriInterface getUri()
 * @method static \App\Kernel\Http\Request withUri(UriInterface $uri, $preserveHost = false)
 * @method static array getServerParams()
 * @method static array getCookieParams()
 * @method static \App\Kernel\Http\Request withCookieParams(array $cookies)
 * @method static array getQueryParams()
 * @method static \App\Kernel\Http\Request withQueryParams(array $query)
 * @method static array getUploadedFiles()
 * @method static \App\Kernel\Http\Request withUploadedFiles(array $uploaded_files)
 * @method static array|null|object getParsedBody()
 * @method static \App\Kernel\Http\Request withParsedBody($data)
 * @method static array getAttributes()
 * @method static mixed|null getAttribute($name, $default = null)
 * @method static \App\Kernel\Http\Request withAttribute($name, $value)
 * @method static \App\Kernel\Http\Request withoutAttribute($name)
 * @method static string|null server(string $name, $default = null)
 * @method static array|string|null header(string $name, $default = null)
 * @method static array all()
 * @method static string|array|null input($key = null, $default = null)
 * @method static string|array|null query($key = null, $default = null)
 * @method static bool has($key)
 * @method static string|array|null cookie($key = null, $default = null)
 * @method static mixed session($name = null, $default = null)
 * @method static string path()
 * @method static string url()
 * @method static string fullUrl()
 * @method static string method()
 * @method static bool isMethod(string $method)
 * @method static UploadFile|null file(string $name)
 * @method static bool hasFile(string $name)
 * @method static bool pattern(string $regex)
 * @method static bool ajax()
 *
 * @see \App\Kernel\Http\Request
 */
class Request extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Kernel\Http\Request::class;
    }
}
