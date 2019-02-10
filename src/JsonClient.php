<?php


namespace TFarla\KongClient;

use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Exception\ServerErrorException;
use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class JsonClient
 * @package TFarla\KongClient
 */
class JsonClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * JsonClient constructor.
     * @param HttpClient $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        HttpClient $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $queryParams
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function get(string $uri, array $headers = [], array $queryParams = [])
    {
        return $this->send('GET', $uri, $headers, $queryParams);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $queryParams
     * @param mixed|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function post(string $uri, array $headers = [], array $queryParams = [], $body = null)
    {
        return $this->send('POST', $uri, $headers, $queryParams, $body);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $queryParams
     * @param mixed|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function put(string $uri, array $headers = [], array $queryParams = [], $body = null)
    {
        return $this->send('PUT', $uri, $headers, $queryParams, $body);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $queryParams
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function delete(string $uri, array $headers = [], array $queryParams = [])
    {
        return $this->send('DELETE', $uri, $headers, $queryParams);
    }

    /**
     * @return mixed
     * @param ResponseInterface $response
     */
    public function readBody(ResponseInterface $response)
    {
        return Json::decode($response->getBody()->getContents());
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array $queryParams
     * @param null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function send(string $method, string $uri, array $headers = [], array $queryParams = [], $body = null)
    {
        $uri = $this->withQueryString($uri, $queryParams);

        $request = $this->requestFactory->createRequest($method, $uri);
        $request = $this->withHeaders($request, $headers);
        $request = $this->prepareRequest($request);

        if (!is_null($body)) {
            $body = $this->streamFactory->createStream(Json::encode($body));
            // some process might already have read the stream (during tests)
            // we should therefore rewind if that's the case
            if ($body->eof() || ($body->tell() === $body->getSize())) {
                $body->rewind();
            }

            $request = $request->withBody($body);
        }

        $response = $this->httpClient->sendRequest($request);
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            return $response;
        }

        if ($statusCode >= 400 && $statusCode < 500) {
            $message = $this->getErrorMessageFromResponse($response);
            throw new ClientErrorException($message, $request, $response);
        }

        if ($statusCode >= 500) {
            $message = $this->getErrorMessageFromResponse($response);
            throw new ServerErrorException($message, $request, $response);
        }

        throw new HttpException($response->getReasonPhrase(), $request, $response);
    }

    /**
     * @param RequestInterface $request
     * @param array $headers
     * @return RequestInterface
     */
    private function withHeaders(RequestInterface $request, array $headers): RequestInterface
    {
        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }

    /**
     * @param string $uri
     * @param array $queryParams
     * @return string
     */
    private function withQueryString(string $uri, array $queryParams): string
    {
        if (count($queryParams) === 0) {
            return $uri;
        }

        $queryString = http_build_query($queryParams);

        return $uri . "?$queryString";
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    private function prepareRequest(RequestInterface $request): RequestInterface
    {
        $contentType = 'application/json charset=utf-8';
        return $request
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Accept', $contentType);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    private function getErrorMessageFromResponse(ResponseInterface $response)
    {
        $body = $response->getBody();
        $contents = $body->getContents();
        $body = Json::decode($contents);
        $response->getBody()->rewind();
        $message = $body['message'];

        return $message;
    }
}
