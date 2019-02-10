<?php

namespace Test\EndToEnd\KongClient;

use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\Formatter;
use PHPUnit\Framework\TestCase;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use TFarla\KongClient\KongClient;
use TFarla\KongClient\Service;

class KongClientTest extends TestCase
{
    /**
     * @var HttpClient
     */
    private $adapter;

    /**
     * @var KongClient
     */
    private $kong;

    protected function setUp(): void
    {
        parent::setUp();

        $client = GuzzleAdapter::createWithConfig([
            'base_uri' => getenv('KONG_BASE_URL')
        ]);

        $formatter = new class implements Formatter
        {

            /**
             * Formats a request.
             *
             * @param RequestInterface $request
             *
             * @return string
             */
            public function formatRequest(RequestInterface $request)
            {
                $content = $request->getBody()->getContents();
                $request->getBody()->rewind();

                return sprintf(
                    '%s %s %s %s',
                    $request->getMethod(),
                    $request->getUri()->__toString(),
                    $request->getProtocolVersion(),
                    $content
                );
            }

            /**
             * Formats a response.
             *
             * @param ResponseInterface $response
             *
             * @return string
             */
            public function formatResponse(ResponseInterface $response)
            {
                $content = $response->getBody()->getContents();
                $response->getBody()->rewind();

                return sprintf(
                    '%s %s %s %s',
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                    $response->getProtocolVersion(),
                    $content
                );
            }
        };

        $this->adapter = new PluginClient($client, [
            new LoggerPlugin(
                new ConsoleLogger(
                    new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG)
                ),
                new $formatter()
            )
        ]);

        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->kong = new KongClient(
            $this->adapter,
            $requestFactory,
            Psr17FactoryDiscovery::findStreamFactory()
        );

        foreach ($this->kong->getServices()->getData() as $service) {
            $id = $service->getId();
            if ($id) {
                $this->kong->deleteService($id);
            }
        }

        foreach ($this->kong->getRoutes()->getData() as $route) {
            $id = $route->getId();
            if ($id) {
                $this->kong->deleteRoute($id);
            }
        }
    }

    /** @test */
    public function itShouldConfigureService()
    {
        $service = new Service();
        $service->setName('test2');
        $service->setHost('example.com');
        $service->setPort(80);
        $service->setProtocol('http');

        $actual = $this->kong->postService($service);
        $this->assertEquals(
            [
                $service->getName(),
                $service->getHost(),
                $service->getPort(),
                $service->getProtocol()
            ],
            [
                $actual->getName(),
                $actual->getHost(),
                $actual->getPort(),
                $actual->getProtocol()
            ]
        );
    }
}
