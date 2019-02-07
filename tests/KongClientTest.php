<?php

namespace Test\KongClient;

use Http\Mock\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use TFarla\KongClient\KongClient;
use TFarla\KongClient\ServiceTransformer;

class KongClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $mockClient;
    /**
     * @var Psr17Factory
     */
    private $requestFactory;
    /**
     * @var KongClient
     */
    private $kong;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockClient = new Client();
        $this->requestFactory = new Psr17Factory();
        $this->kong = new KongClient($this->mockClient, $this->requestFactory);
    }

    /**
     * @dataProvider servicesFixtureProvider
     * @test
     * @param string $fixture
     */
    public function itShouldGetServices(string $fixture)
    {
        $this->addMockResponse($fixture);

        $actual = $this->kong->getServices();
        $data = [];
        foreach ($actual->getData() as $service) {
            $data[] = ServiceTransformer::toArray($service);
        }
        $json = json_encode(['data' => $data, 'next' => $actual->getNext()]);
        if (!$json) {
            throw new \Exception('Serialization failed');
        }

        $this->assertJsonStringEqualsJsonFile(
            $this->fixture($fixture),
            $json
        );
    }

    /** @test */
    public function itShouldGetService()
    {
        $fixture = 'service.json';
        $this->addMockResponse($fixture);

        $serviceName = 'test';
        $actual = $this->kong->getService('test');
        if ($actual === null) {
            throw new \Exception('Service not found');
        }

        $json = json_encode(ServiceTransformer::toArray($actual));
        if (!$json) {
            throw new \Exception('Json serialization failed.');
        }

        $this->assertJsonStringEqualsJsonFile(
            $this->fixture($fixture),
            $json
        );

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertEquals($lastRequest->getHeader('Accept'), ['application/json charset=utf-8']);
        $this->assertEquals($lastRequest->getHeader('Content-Type'), ['application/json charset=utf-8']);
        $this->assertSame("/services/$serviceName", $lastRequest->getUri()->getPath());
    }

    public function servicesFixtureProvider()
    {
        return [
            ['services-empty.json'],
            ['services.json']
        ];
    }

    public function fixture($name): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $name]);
    }

    /**
     * @param string $fixture
     */
    private function addMockResponse(string $fixture): void
    {
        $fixture = $this->fixture($fixture);
        $body = $this->requestFactory->createStreamFromFile($fixture);
        $response = $this->requestFactory->createResponse()->withBody($body);
        $this->mockClient->addResponse($response);
    }
}
