<?php

namespace Test;

use App\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use function env;

/**
 * Class HttpTestCase
 * @package Test
 *
 * @method Response get(string|UriInterface $uri, array $options = [])
 * @method Response head(string|UriInterface $uri, array $options = [])
 * @method Response put(string|UriInterface $uri, array $options = [])
 * @method Response post(string|UriInterface $uri, array $options = [])
 * @method Response patch(string|UriInterface $uri, array $options = [])
 * @method Response delete(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface getAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface headAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface putAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface postAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface patchAsync(string|UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface deleteAsync(string|UriInterface $uri, array $options = [])
 * @method Response send(RequestInterface $request, array $options = [])
 * @method Promise\PromiseInterface sendAsync(RequestInterface $request, array $options = [])
 * @method Response request($method, $uri, array $options = [])
 * @method Promise\PromiseInterface requestAsync($method, $uri, array $options = [])
 * @method mixed getConfig($option = null)
 *
 * @see \GuzzleHttp\Client
 */
abstract class HttpTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $port = env('APP_PORT', 8080);
        $url = env('APP_URL', 'http://localhost');
        $this->client = new Client([
            'base_uri' => "$url:$port",
            'http_errors' => false
        ]);
    }

    public function __call($name, $arguments)
    {
        $response = $this->client->$name(...$arguments);
        if ($response instanceof ResponseInterface) {
            return new Response(
                $response->getBody(),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        }
        return $response;
    }
}
