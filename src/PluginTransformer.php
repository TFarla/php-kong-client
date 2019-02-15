<?php


namespace TFarla\KongClient;

class PluginTransformer
{
    /**
     * @param Plugin $plugin
     * @return array
     */
    public static function toRequestBody(Plugin $plugin): array
    {
        $requestBody = [
            'name' => $plugin->getName(),
            'config' => $plugin->getConfig(),
            'run_on' => $plugin->getRunOn(),
            'enabled' => $plugin->isEnabled(),
        ];

        $serviceId = $plugin->getServiceId();
        if (!is_null($serviceId)) {
            $requestBody['service'] = ['id' => $serviceId];
        }

        $routeId = $plugin->getRouteId();
        if (!is_null($routeId)) {
            $requestBody['route'] = ['id' => $routeId];
        }

        return $requestBody;
    }

    /**
     * @param array $values
     * @return Plugin
     */
    public static function fromResponseBody(array $values): Plugin
    {
        $plugin = new Plugin();

        $plugin->setId($values['id']);
        $plugin->setName($values['name']);
        $plugin->setConfig($values['config']);
        $plugin->setCreatedAt($values['created_at']);
        $plugin->setEnabled($values['enabled']);
        $plugin->setRunOn($values['run_on']);

        if (isset($values['service']) && !is_null($values['service'])) {
            $service = $values['service'];
            $plugin->setServiceId($service['id'] ?? null);
        }

        if (isset($values['route']) && !is_null($values['route'])) {
            $route = $values['route'];
            $plugin->setRouteId($route['id'] ?? null);
        }

        return $plugin;
    }
}
