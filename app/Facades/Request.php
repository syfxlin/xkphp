<?php

namespace App\Facades;

use App\Http\UploadFile;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package App\Facades
 *
 * @method static string getProtocolVersion()
 * @method static \App\Http\Request withProtocolVersion($version)
 * @method static \App\Http\Request setProtocolVersion($version)
 * @method static array getHeaders()
 * @method static bool hasHeader($name)
 * @method static array getHeader($name)
 * @method static string getHeaderLine($name)
 * @method static \App\Http\Request withHeader($name, $value)
 * @method static \App\Http\Request withAddedHeader($name, $value)
 * @method static \App\Http\Request withoutHeader($name)
 * @method static \App\Http\Request withHeaders(array $headers)
 * @method static \App\Http\Request setHeader($name, $value)
 * @method static \App\Http\Request setHeaders(array $headers)
 * @method static StreamInterface getBody()
 * @method static \App\Http\Request withBody(StreamInterface $body)
 * @method static \App\Http\Request setBody(StreamInterface $body)
 * @method static string getRequestTarget()
 * @method static \App\Http\Request withRequestTarget($request_target)
 * @method static \App\Http\Request setRequestTarget($request_target)
 * @method static string getMethod()
 * @method static \App\Http\Request withMethod($method)
 * @method static \App\Http\Request setMethod($method)
 * @method static UriInterface getUri()
 * @method static \App\Http\Request withUri(UriInterface $uri, $preserveHost = false)
 * @method static \App\Http\Request setUri(UriInterface $uri, $preserveHost = false)
 * @method static array getServerParams()
 * @method static array getCookieParams()
 * @method static \App\Http\Request withCookieParams(array $cookies)
 * @method static \App\Http\Request setCookieParams(array $cookies)
 * @method static array getQueryParams()
 * @method static \App\Http\Request withQueryParams(array $query)
 * @method static \App\Http\Request setQueryParams(array $query)
 * @method static array getUploadedFiles()
 * @method static \App\Http\Request withUploadedFiles(array $uploaded_files)
 * @method static \App\Http\Request setUploadedFiles(array $uploaded_files)
 * @method static array|null|object getParsedBody()
 * @method static \App\Http\Request withParsedBody($data)
 * @method static \App\Http\Request setParsedBody($data)
 * @method static array getAttributes()
 * @method static array setAttributes(array $attrs)
 * @method static mixed|null getAttribute($name, $default = null)
 * @method static \App\Http\Request withAttribute($name, $value)
 * @method static \App\Http\Request setAttribute($name, $value)
 * @method static \App\Http\Request withoutAttribute($name)
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
 * @see \App\Http\Request
 */
class Request extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Http\Request::class;
    }
}
