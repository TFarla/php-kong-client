<?php

namespace Test\EndToEnd\KongClient;

use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use TFarla\KongClient\KongClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class TestCase extends PHPUnitTestCase
{
    /**
     * @var HttpClient
     */
    protected $adapter;

    /**
     * @var KongClient
     */
    protected $kong;

    /**
     * @throws \Http\Client\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $client = GuzzleAdapter::createWithConfig([
            'base_uri' => getenv('KONG_BASE_URL')
        ]);

        $this->adapter = new PluginClient($client, [
            new LoggerPlugin(
                new ConsoleLogger(
                    new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG)
                ),
                new TestFormatter()
            )
        ]);

        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->kong = new KongClient(
            $this->adapter,
            $requestFactory,
            Psr17FactoryDiscovery::findStreamFactory()
        );

        $this->deletePlugins();
        $this->deleteAllServices();
    }

    public function deleteAllServices()
    {
        foreach ($this->kong->getRoutes()->getData() as $route) {
            $id = $route->getId();
            if ($id) {
                $this->kong->deleteRoute($id);
            }
        }

        foreach ($this->kong->getServices()->getData() as $service) {
            $id = $service->getId();
            if ($id) {
                $this->kong->deleteService($id);
            }
        }
    }

    public function deletePlugins()
    {
        foreach ($this->kong->getPlugins()->getData() as $plugin) {
            $id = $plugin->getId();
            if ($id) {
                $this->kong->deletePlugin($id);
            }
        }
    }
}
