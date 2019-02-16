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
use TFarla\KongClient\PaginatedResult;

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

        $this->deleteCertificates();
        $this->deleteConsumers();
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

    public function deleteConsumers()
    {
        foreach ($this->kong->getConsumers()->getData() as $consumer) {
            $id = $consumer->getId();
            if ($id) {
                $this->kong->deleteConsumer($id);
            }
        }
    }

    public function deleteCertificates()
    {
        foreach ($this->kong->getCertificates()->getData() as $certificate) {
            $id = $certificate->getId();
            if ($id) {
                $this->kong->deleteCertificate($id);
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


    /**
     * @param array $items
     * @param callable $getItems
     */
    protected function assertHasPaginationSupport($items, callable $getItems)
    {
        $offset = null;
        $actualItems = [];
        $size = 1;
        for ($i = 0; $i < count($items); $i++) {
            /** @var PaginatedResult $result */
            $result = $getItems($size, $offset);
            $this->assertCount(1, $result->getData());
            $actualItems[] = $result->getData()[0];
            $offset = $result->getOffset();
        }

        sort($items);
        sort($actualItems);

        $this->assertEquals($items, $actualItems);
    }
}
