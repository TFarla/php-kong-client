<?php

declare(strict_types=1);

namespace TFarla\KongClient;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class KongClient
{
    /**
     * @var JsonClient
     */
    private $jsonClient;

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
        $this->jsonClient = new JsonClient(
            $httpClient,
            $requestFactory,
            $streamFactory
        );
    }

    /**
     * @return ServicePaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getServices(): ServicePaginatedResult
    {
        $response = $this->jsonClient->get('/services');
        $body = $this->jsonClient->readBody($response);

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
        $response = $this->jsonClient->get("/services/$nameOrId");
        $body = $this->jsonClient->readBody($response);

        return ServiceTransformer::fromJson($body);
    }

    /**
     * @param Service $service
     * @return Service
     */
    public function postService(Service $service): Service
    {
        $rawService = ServiceTransformer::toArray($service);
        $response = $this->jsonClient->post('/services', [], [], $rawService);
        $body = $this->jsonClient->readBody($response);

        return ServiceTransformer::fromJson($body);
    }

    public function putService(Service $service): Service
    {
        $id = $service->getId();
        if (is_null($id)) {
            throw new \InvalidArgumentException('Can not update a service when it has no id');
        }

        $uri = "/services/$id";
        $rawService = ServiceTransformer::toArray($service);
        $response = $this->jsonClient->put($uri, [], [], $rawService);
        $body = $this->jsonClient->readBody($response);

        return ServiceTransformer::fromJson($body);
    }

    public function deleteService(string $id): void
    {
        $uri = "/services/$id";
        $this->jsonClient->delete($uri);
    }
}
