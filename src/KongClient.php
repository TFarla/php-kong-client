<?php


namespace TFarla\KongClient;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;

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
     * KongClient constructor.
     * @param HttpClient $httpClient
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(HttpClient $httpClient, RequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
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
        $body = json_decode($response->getBody(), true);
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

        $body = json_decode($response->getBody(), true);
        return ServiceTransformer::fromJson($body);
    }
}
