<?php

declare(strict_types=1);

namespace TFarla\KongClient;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class KongClient
 * @package TFarla\KongClient
 */
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
     * @param int|null $size
     * @return ServicePaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getServices(?int $size = null, string $offset = null): ServicePaginatedResult
    {
        $queryParams = [];
        if (!is_null($size)) {
            $queryParams['size'] = $size;
        }

        if (!is_null($offset)) {
            $queryParams['offset'] = $offset;
        }


        $response = $this->jsonClient->get('/services', [], $queryParams);
        $body = $this->jsonClient->readBody($response);

        $next = $body['next'] ?? null;
        $offset = $body['offset'] ?? null;
        $data = [];
        foreach ($body['data'] as $rawService) {
            $data[] = ServiceTransformer::fromJson($rawService);
        }

        $result = new ServicePaginatedResult($data, $next, $offset);

        return $result;
    }

    /**
     * @param string $nameOrId
     * @return Service|null
     * @throws \Http\Client\Exception
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
     * @throws \Http\Client\Exception
     */
    public function postService(Service $service): Service
    {
        $pairs = [
            ['name', $service->getName()],
            ['url', $service->getUrl()],
            ['protocol', $service->getProtocol()],
            ['host', $service->getHost()],
            ['port', $service->getPort()],
            ['path', $service->getPath()],
            ['retries', $service->getRetries()],
            ['read_timeout', $service->getReadTimeout()],
            ['write_timeout', $service->getWriteTimeout()],
            ['connect_timeout', $service->getConnectTimeout()]
        ];

        $requestBody = [];
        foreach ($pairs as list($key, $value)) {
            if (!is_null($value)) {
                $requestBody[$key] = $value;
            }
        }

        $response = $this->jsonClient->post('/services', [], [], $requestBody);
        $body = $this->jsonClient->readBody($response);

        return ServiceTransformer::fromJson($body);
    }

    /**
     * @param Service $service
     * @return Service
     * @throws \Http\Client\Exception
     */
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

    /**
     * @param string $id
     * @throws \Http\Client\Exception
     */
    public function deleteService(string $id): void
    {
        $uri = "/services/$id";
        $this->jsonClient->delete($uri);
    }

    /**
     * @param int|null $size
     * @param string|null $offset
     * @return RoutePaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getRoutes(?int $size = null, ?string $offset = null): RoutePaginatedResult
    {
        $queryParams = [];
        if (!is_null($size)) {
            $queryParams['size'] = $size;
        }

        if (!is_null($offset)) {
            $queryParams['offset'] = $offset;
        }

        $response = $this->jsonClient->get('/routes', [], $queryParams);
        $body = $this->jsonClient->readBody($response);
        $next = $body['next'] ?? null;
        $offset = $body['offset'] ?? null;

        $routes = [];
        foreach (($body['data'] ?? []) as $rawRoute) {
            $route = RouteTransformer::fromArray($rawRoute);
            $routes[] = $route;
        }

        return new RoutePaginatedResult($routes, $next, $offset);
    }

    /**
     * @param string $id
     * @return Route
     * @throws \Http\Client\Exception
     */
    public function getRoute(string $id): Route
    {
        $response = $this->jsonClient->get("/routes/$id");
        $body = $this->jsonClient->readBody($response);

        return RouteTransformer::fromArray($body);
    }

    /**
     * @param Route $route
     * @return Route
     * @throws \Http\Client\Exception
     */
    public function postRoute(Route $route): Route
    {
        $requestBody = RouteTransformer::toArray($route);
        $response = $this->jsonClient->post('/routes', [], [], $requestBody);
        $body = $this->jsonClient->readBody($response);

        return RouteTransformer::fromArray($body);
    }

    /**
     * @param Route $route
     * @return Route
     * @throws \Http\Client\Exception
     */
    public function putRoute(Route $route): Route
    {
        $id = $route->getId();
        if (is_null($id)) {
            throw new \InvalidArgumentException('Can not update a route when it has no id');
        }

        $uri = "/routes/$id";
        $requestBody = RouteTransformer::toArray($route);
        $response = $this->jsonClient->put($uri, [], [], $requestBody);
        $body = $this->jsonClient->readBody($response);

        return RouteTransformer::fromArray($body);
    }

    /**
     * @param string $id
     * @throws \Http\Client\Exception
     */
    public function deleteRoute(string $id): void
    {
        $uri = "/routes/$id";
        $this->jsonClient->delete($uri);
    }

    /**
     * @param int|null $size
     * @param string|null $offset
     * @return PluginPaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getPlugins(?int $size = null, ?string $offset = null): PluginPaginatedResult
    {
        return $this->doGetPlugins("/plugins", $size, $offset);
    }

    /**
     * @param string $routeId
     * @param int|null $size
     * @param string|null $offset
     * @return PluginPaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getPluginsForRoute(
        string $routeId,
        ?int $size = null,
        ?string $offset = null
    ): PluginPaginatedResult {
        return $this->doGetPlugins("/routes/$routeId/plugins", $size, $offset);
    }

    /**
     * @param string $serviceId
     * @param int|null $size
     * @param string|null $offset
     * @return PluginPaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getPluginsForService(
        string $serviceId,
        ?int $size = null,
        ?string $offset = null
    ): PluginPaginatedResult {
        return $this->doGetPlugins("/services/$serviceId/plugins", $size, $offset);
    }

    /**
     * @param string $consumerId
     * @param int|null $size
     * @param string|null $offset
     * @return PluginPaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getPluginsForConsumer(string $consumerId, ?int $size = null, ?string $offset = null)
    {
        return $this->doGetPlugins("/consumers/$consumerId/plugins", $size, $offset);
    }

    /**
     * @param string $uri
     * @return PluginPaginatedResult
     * @throws \Http\Client\Exception
     */
    private function doGetPlugins(string $uri, ?int $size = null, ?string $offset = null)
    {
        $queryParams = [];
        if (!is_null($size)) {
            $queryParams['size'] = $size;
        }

        if (!is_null($offset)) {
            $queryParams['offset'] = $offset;
        }

        $data = [];
        $resp = $this->jsonClient->get($uri, [], $queryParams);
        $body = $this->jsonClient->readBody($resp);

        $next = $body['next'] ?? null;
        $offset = $body['offset'] ?? null;

        foreach ($body['data'] as $rawPlugin) {
            $data[] = PluginTransformer::fromResponseBody($rawPlugin);
        }

        $result = new PluginPaginatedResult($data, $next, $offset);

        return $result;
    }

    /**
     * @param string $id
     * @return Plugin
     * @throws \Http\Client\Exception
     */
    public function getPlugin(string $id): Plugin
    {
        $resp = $this->jsonClient->get("/plugins/$id");
        $body = $this->jsonClient->readBody($resp);

        return PluginTransformer::fromResponseBody($body);
    }

    /**
     * @param Plugin $plugin
     * @return Plugin
     * @throws \Http\Client\Exception
     */
    public function postPlugin(Plugin $plugin): Plugin
    {
        $requestBody = PluginTransformer::toRequestBody($plugin);
        $resp = $this->jsonClient->post('/plugins', [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);

        return PluginTransformer::fromResponseBody($body);
    }

    /**
     * @param Plugin $plugin
     * @return Plugin
     * @throws \Http\Client\Exception
     */
    public function putPlugin(Plugin $plugin): Plugin
    {
        $uri = "/plugins/{$plugin->getId()}";
        $requestBody = PluginTransformer::toRequestBody($plugin);
        $resp = $this->jsonClient->put($uri, [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);

        return PluginTransformer::fromResponseBody($body);
    }

    /**
     * @param string $id
     * @throws \Http\Client\Exception
     */
    public function deletePlugin(string $id): void
    {
        $this->jsonClient->delete("/plugins/$id");
    }

    /**
     * @param int|null $size
     * @param string|null $offset
     * @return ConsumerPaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getConsumers(?int $size = null, ?string $offset = null): ConsumerPaginatedResult
    {
        $queryParams = [];
        if (!is_null($size)) {
            $queryParams['size'] = $size;
        }

        if (!is_null($offset)) {
            $queryParams['offset'] = $offset;
        }

        $resp = $this->jsonClient->get('/consumers', [], $queryParams);
        $body = $this->jsonClient->readBody($resp);

        $data = [];
        $next = $body['next'] ?? null;
        $offset = $body['offset'] ?? null;
        foreach ($body['data'] as $rawConsumer) {
            $data[] = ConsumerTransformer::fromResponseBody($rawConsumer);
        }


        $result = new ConsumerPaginatedResult($data, $next, $offset);

        return $result;
    }

    /**
     * @param Consumer $consumer
     * @return Consumer
     * @throws \Http\Client\Exception
     */
    public function postConsumer(Consumer $consumer): Consumer
    {
        $requestBody = ConsumerTransformer::toRequest($consumer);
        $resp = $this->jsonClient->post('/consumers', [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);
        return ConsumerTransformer::fromResponseBody($body);
    }

    /**
     * @param string $id
     * @throws \Http\Client\Exception
     */
    public function deleteConsumer(string $id): void
    {
        $this->jsonClient->delete("/consumers/$id");
    }

    /**
     * @param Consumer $consumer
     * @return Consumer
     * @throws \Http\Client\Exception
     */
    public function putConsumer(Consumer $consumer): Consumer
    {
        $uri = "/consumers/{$consumer->getId()}";
        $requestBody = ConsumerTransformer::toRequest($consumer);

        $resp = $this->jsonClient->put($uri, [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);

        return ConsumerTransformer::fromResponseBody($body);
    }

    /**
     * @param Certificate $certificate
     * @return Certificate
     * @throws \Http\Client\Exception
     */
    public function postCertificate(Certificate $certificate): Certificate
    {
        $requestBody = CertificateTransformer::toRequestBody($certificate);

        $resp = $this->jsonClient->post('/certificates', [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);

        return CertificateTransformer::fromResponseBody($body);
    }

    /**
     * @param string $id
     * @return Certificate
     * @throws \Http\Client\Exception
     */
    public function getCertificate(string $id): Certificate
    {
        $resp = $this->jsonClient->get("/certificates/$id");
        $body = $this->jsonClient->readBody($resp);

        return CertificateTransformer::fromResponseBody($body);
    }

    /**
     * @param Certificate $certificate
     * @return Certificate
     * @throws \Http\Client\Exception
     */
    public function putCertificate(Certificate $certificate): Certificate
    {
        $id = $certificate->getId();
        $uri = "/certificates/$id";
        $requestBody = CertificateTransformer::toRequestBody($certificate);
        $resp = $this->jsonClient->put($uri, [], [], $requestBody);
        $body = $this->jsonClient->readBody($resp);

        return CertificateTransformer::fromResponseBody($body);
    }

    /**
     * @param int|null $size
     * @param string|null $offset
     * @return CertificatePaginatedResult
     * @throws \Http\Client\Exception
     */
    public function getCertificates(?int $size = null, ?string $offset = null): CertificatePaginatedResult
    {
        $queryParams = [];
        if (!is_null($size)) {
            $queryParams['size'] = $size;
        }

        if (!is_null($offset)) {
            $queryParams['offset'] = $offset;
        }

        $resp = $this->jsonClient->get('/certificates', [], $queryParams);
        $body = $this->jsonClient->readBody($resp);
        $next = $body['next'] ?? null;
        $offset = $body['offset'] ?? null;
        $certificates = [];
        foreach ($body['data'] as $rawCertificate) {
            $certificates[] = CertificateTransformer::fromResponseBody($rawCertificate);
        }

        return new CertificatePaginatedResult($certificates, $next, $offset);
    }

    /**
     * @param string $id
     * @throws \Http\Client\Exception
     */
    public function deleteCertificate(string $id): void
    {
        $this->jsonClient->delete("/certificates/$id");
    }
}
