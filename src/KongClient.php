<?php

declare(strict_types=1);

namespace TFarla\KongClient;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class KongClient
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
     * KongClient constructor.
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
     * @return ServicePaginatedResult
     */
    public function getServices(): ServicePaginatedResult
    {
        $request = $this->requestFactory->createRequest('GET', '/services')
            ->withHeader('Content-Type', 'application/json charset=utf-8')
            ->withHeader('Accept', 'application/json charset=utf-8');

        $response = $this->httpClient->sendRequest($request);
        $body = json_decode($response->getBody()->getContents(), true);
        $next = $body['next'] ?? null;
        $data = [];
        foreach ($body['data'] as $rawService) {
            $data[] = ServiceTransformer::fromJson($rawService);
        }

        $result = new ServicePaginatedResult($data, $next);

        return $result;
    }

    /**
     * @param string $nameOrId
     * @return Service|null
     */
    public function getService(string $nameOrId): ?Service
    {
        $request = $this->requestFactory->createRequest('GET', "/services/$nameOrId")
            ->withHeader('Content-Type', 'application/json charset=utf-8')
            ->withHeader('Accept', 'application/json charset=utf-8');

        $response = $this->httpClient->sendRequest($request);

        $body = json_decode($response->getBody()->getContents(), true);
        return ServiceTransformer::fromJson($body);
    }

    /**
     * @param Service $service
     * @return Service
     */
    public function postService(Service $service): Service
    {
        $rawService = ServiceTransformer::toArray($service);
        $body = $this->streamFactory->createStream(Json::encode($rawService));
        // some process might already have read the stream (during tests)
        // we should therefore rewind if that's the case
        if ($body->eof() || ($body->tell() === $body->getSize())) {
            $body->rewind();
        }

        $request = $this->requestFactory->createRequest('POST', '/services')
            ->withBody($body)
            ->withHeader('Content-Type', 'application/json charset=utf-8')
            ->withHeader('Accept', 'application/json charset=utf-8');

        $response = $this->httpClient->sendRequest($request);
        $body = Json::decode($response->getBody()->getContents());
        return ServiceTransformer::fromJson($body);
    }
}
