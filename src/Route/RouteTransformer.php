<?php


namespace TFarla\KongClient\Route;

use TFarla\KongClient\Route;

/**
 * Class RouteTransformer
 * @package TFarla\KongClient\Route
 */
class RouteTransformer
{
    /**
     * @param Route $route
     * @return array
     */
    public static function toArray(Route $route): array
    {
        $data = [
            'id' => $route->getId(),
            'created_at' => $route->getCreatedAt(),
            'updated_at' => $route->getUpdateAt(),
            'name' => $route->getName(),
            'protocols' => $route->getProtocols(),
            'methods' => $route->getMethods(),
            'hosts' => $route->getHosts(),
            'paths' => $route->getPaths(),
            'regex_priority' => $route->getRegexPriority(),
            'preserve_host' => $route->isPreserveHost(),
            'strip_path' => $route->isStripPath(),
            'snis' => $route->getSnis(),
        ];

        $serviceId = $route->getServiceId();
        if (!is_null($serviceId)) {
            $data['service'] = ['id' => $serviceId];
        }

        foreach ($data as $key => $value) {
            if (is_null($value)) {
                unset($data[$key]);
            }
        }

        $rawSources = static::toTargetsArray($route->getSources());
        if (!is_null($rawSources)) {
            $data['sources'] = $rawSources;
        }

        $rawDestinations = static::toTargetsArray($route->getDestinations());
        if (!is_null($rawDestinations)) {
            $data['destinations'] = $rawDestinations;
        }

        return $data;
    }

    /**
     * @param array $rawRoute
     * @return Route
     */
    public static function fromArray(array $rawRoute): Route
    {
        $route = new Route();
        $service = $rawRoute['service'] ?? new \stdClass();

        $route->setId($rawRoute['id'] ?? null);
        $route->setCreatedAt($rawRoute['created_at'] ?? null);
        $route->setUpdateAt($rawRoute['updated_at'] ?? null);
        $route->setName($rawRoute['name'] ?? null);
        $route->setProtocols($rawRoute['protocols'] ?? null);
        $route->setMethods($rawRoute['methods'] ?? null);
        $route->setHosts($rawRoute['hosts'] ?? null);
        $route->setPaths($rawRoute['paths'] ?? null);
        $route->setRegexPriority($rawRoute['regex_priority'] ?? null);
        $route->setPreserveHost($rawRoute['preserve_host']);
        $route->setStripPath($rawRoute['strip_path']);
        $route->setSnis($rawRoute['snis'] ?? null);

        $route->setServiceId($service['id'] ?? null);

        $sources = static::fromArrayToTargets('sources', $rawRoute);
        $route->setSources($sources);

        $destinations = static::fromArrayToTargets('destinations', $rawRoute);
        $route->setDestinations($destinations);

        return $route;
    }

    /**
     * @param string $key
     * @param array $rawRoute
     * @return Target[]|null
     */
    private static function fromArrayToTargets($key, $rawRoute): ?array
    {
        $targets = null;
        if (isset($rawRoute[$key]) && is_array($rawRoute[$key])) {
            $targets = [];
            foreach ($rawRoute[$key] as $target) {
                $targets[] = new Target($target['ip'] ?? null, $target['port'] ?? null);
            }
        }

        if (is_null($targets) || count($targets) === 0) {
            return null;
        }

        return $targets;
    }

    /**
     * @param array|null $targets
     * @return array|null
     */
    private static function toTargetsArray(?array $targets)
    {
        if (is_null($targets) || count($targets) === 0) {
            return $targets;
        }

        $rawTargets = null;
        foreach ($targets as $target) {
            $rawTarget = [];
            $port = $target->getPort();
            if (!is_null($port)) {
                $rawTarget['port'] = $target->getPort();
            }

            $ip = $target->getIp();
            if (!is_null($ip)) {
                $rawTarget['ip'] = $ip;
            }

            $rawTargets[] = $rawTarget;
        }

        return $rawTargets;
    }
}
