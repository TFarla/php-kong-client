<?php


namespace Test\EndToEnd\KongClient;

use TFarla\KongClient\KongClient;
use TFarla\KongClient\Plugin;
use TFarla\KongClient\PluginPaginatedResult;
use TFarla\KongClient\PluginTransformer;
use TFarla\KongClient\Route;
use TFarla\KongClient\Service;

class PluginTest extends TestCase
{
    /**
     * @var string
     */
    private $serviceId;
    /**
     * @var string
     */
    private $routeId;

    protected function setUp(): void
    {
        parent::setUp();

        $service = new Service();
        $service->setName('test');
        $service->setUrl('http://localhost:8001');

        $service = $this->kong->postService($service);

        $route = new Route();
        $route->setServiceId($service->getId());
        $route->setPaths(['/']);
        $route = $this->kong->postRoute($route);

        if (!is_null($service->getId())) {
            $this->serviceId = $service->getId();
        }

        if (!is_null($route->getId())) {
            $this->routeId = $route->getId();
        }
    }

    /** @test */
    public function itShouldGetPlugins(): void
    {
        $result = $this->kong->getPlugins();

        $this->assertCount(0, $result->getData());
        $this->assertNull($result->getNext());
        $this->assertNull($result->getOffset());
    }

    /**
     * @test
     */
    public function itShouldSupportCursorBasedPagination()
    {
        $plugins = [];
        $pluginNames = ['cors', 'bot-detection'];
        foreach ($pluginNames as $pluginName) {
            $plugin = new Plugin();
            $plugin->setName($pluginName);
            $plugins[] = $this->kong->postPlugin($plugin);
        }

        $this->assertHasPaginationSupport($plugins, function ($size, $offset) {
            return $this->kong->getPlugins($size, $offset);
        });
    }

    /**
     * @test
     */
    public function itShouldSupportCursorBasedPaginationForServicePlugins()
    {
        $plugins = [];
        $pluginNames = ['cors', 'bot-detection'];
        foreach ($pluginNames as $pluginName) {
            $plugin = new Plugin();
            $plugin->setName($pluginName);
            $plugin->setServiceId($this->serviceId);
            $plugins[] = $this->kong->postPlugin($plugin);
        }

        $this->assertHasPaginationSupport($plugins, function ($size, $offset) {
            return $this->kong->getPluginsForService($this->serviceId, $size, $offset);
        });
    }

    /**
     * @test
     */
    public function itShouldSupportCursorBasedPaginationForRoutePlugins()
    {
        $plugins = [];
        $pluginNames = ['cors', 'bot-detection'];
        foreach ($pluginNames as $pluginName) {
            $plugin = new Plugin();
            $plugin->setName($pluginName);
            $plugin->setRouteId($this->routeId);
            $plugins[] = $this->kong->postPlugin($plugin);
        }

        $this->assertHasPaginationSupport($plugins, function ($size, $offset) {
            return $this->kong->getPluginsForRoute($this->routeId, $size, $offset);
        });
    }

    private function assertHasPaginationSupport($plugins, callable $getPlugins)
    {
        $offset = null;
        $actualPlugins = [];
        $size = 1;
        for ($i = 0; $i < count($plugins); $i++) {
            /** @var PluginPaginatedResult $result */
            $result = $getPlugins($size, $offset);
            $this->assertCount(1, $result->getData());
            $actualPlugins[] = $result->getData()[0];
            $offset = $result->getOffset();
        }

        sort($plugins);
        sort($actualPlugins);

        $this->assertEquals($plugins, $actualPlugins);
    }

    /**
     * @dataProvider pluginProvider
     * @test
     * @param Plugin $plugin
     * @throws \Http\Client\Exception
     */
    public function itShouldGetPluginsForService(Plugin $plugin): void
    {
        $this->kong->postPlugin($plugin);
        $plugin->setServiceId($this->serviceId);
        $createdPlugin = $this->kong->postPlugin($plugin);
        $result = $this->kong->getPluginsForService($this->serviceId);
        $this->assertEquals([$createdPlugin], $result->getData());
        $this->assertNull($result->getNext());
        $this->assertNull($result->getOffset());
    }

    /**
     * @dataProvider pluginProvider
     * @test
     * @param Plugin $plugin
     * @throws \Http\Client\Exception
     */
    public function itShouldGetPluginsForRoute(Plugin $plugin): void
    {
        $this->kong->postPlugin($plugin);
        $plugin->setRouteId($this->routeId);
        $createdPlugin = $this->kong->postPlugin($plugin);
        $result = $this->kong->getPluginsForRoute($this->routeId);
        $this->assertEquals([$createdPlugin], $result->getData());
        $this->assertNull($result->getNext());
        $this->assertNull($result->getOffset());
    }

    /**
     * @dataProvider pluginProvider
     * @test
     * @param Plugin $plugin
     * @throws \Http\Client\Exception
     */
    public function itShouldGetPlugin(Plugin $plugin)
    {
        $createdPlugin = $this->kong->postPlugin($plugin);
        $id = $createdPlugin->getId();
        if (is_null($id)) {
            throw new \UnexpectedValueException('plugin id should not be null');
        }

        $actualPlugin = $this->kong->getPlugin($id);
        $this->assertEquals($createdPlugin, $actualPlugin);
    }

    /**
     * @dataProvider pluginProvider
     * @test
     * @param Plugin $plugin
     */
    public function itShouldBePossibleToInstall(Plugin $plugin): void
    {
        $createdPlugin = $this->kong->postPlugin($plugin);
        $id = $createdPlugin->getId();
        $plugin->setId($id);
        $plugin->setCreatedAt($createdPlugin->getCreatedAt());

        $this->assertIsString($id);
        $this->assertNull($plugin->getRouteId());
        $this->assertNull($plugin->getServiceId());
        $this->assertNull($plugin->getConsumerId());
        $this->assertNotNull($createdPlugin->getCreatedAt());
        $this->assertEquals($plugin, $createdPlugin);
    }

    /** @test */
    public function itShouldBePossibleToInstallAPluginOnAService()
    {
        $plugin = new Plugin();
        $plugin->setName('cors');
        $plugin->setServiceId($this->serviceId);

        $createdPlugin = $this->kong->postPlugin($plugin);
        $this->assertSame($this->serviceId, $createdPlugin->getServiceId());
    }


    /** @test */
    public function itShouldBePossibleToInstallAPluginOnARoute()
    {
        $plugin = new Plugin();
        $plugin->setName('cors');
        $plugin->setRouteId($this->routeId);

        $createdPlugin = $this->kong->postPlugin($plugin);
        $this->assertSame($this->routeId, $createdPlugin->getRouteId());
    }

    /**
     * @dataProvider pluginProvider
     * @test
     */
    public function itShouldPutPlugin(Plugin $plugin, array $updatedConfig)
    {
        $plugin = $this->kong->postPlugin($plugin);
        $updatedPlugin = clone $plugin;
        $newConfig = $updatedConfig + $plugin->getConfig();
        $updatedPlugin->setConfig($newConfig);
        $updatedPlugin = $this->kong->putPlugin($updatedPlugin);
        $this->assertEquals($newConfig, $updatedPlugin->getConfig());
    }

    public function pluginProvider()
    {
        $pluginA = new Plugin();
        $pluginA->setName('cors');
        $pluginA->setRunOn('first');
        $pluginA->setConfig([
            'origins' => ['example.com'],
            'methods' => ['GET', 'PUT'],
            'exposed_headers' => null,
            'max_age' => null,
            'headers' => null,
            'credentials' => true,
            'preflight_continue' => true
        ]);

        $pluginB = clone $pluginA;
        $pluginB->setEnabled(false);

        $disabledPlugin = new Plugin();
        $disabledPlugin->setName('bot-detection');
        $disabledPlugin->setEnabled(false);
        $disabledPlugin->setConfig([
            'whitelist' => ['127.0.0.1'],
            'blacklist' => []
        ]);

        return [
            [$pluginA, ['origins' => ['foo.bar']]],
            [$pluginB, ['methods' => []]],
            [$disabledPlugin, ['whitelist' => ['192.168.1.1']]]
        ];
    }
}
