<?php

namespace Test\KongClient;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\StreamFactory;
use Http\Mock\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use TFarla\KongClient\Json;
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
        $this->kong = new KongClient($this->mockClient, $this->requestFactory, $this->requestFactory);
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

        $json = Json::encode(['data' => $data, 'next' => $actual->getNext()]);
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

        $json = Json::encode(ServiceTransformer::toArray($actual));
        $this->assertJsonStringEqualsJsonFile(
            $this->fixture($fixture),
            $json
        );

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertEquals($lastRequest->getHeader('Accept'), ['application/json charset=utf-8']);
        $this->assertEquals($lastRequest->getHeader('Content-Type'), ['application/json charset=utf-8']);
        $this->assertSame("/services/$serviceName", $lastRequest->getUri()->getPath());
    }

    /** @test */
    public function itShouldPostService()
    {
        $fixture = 'service.json';
        $data = $this->fixture($fixture);
        $contents = file_get_contents($data);
        if (!$contents) {
            throw new \Exception('Failed getting contents');
        }

        $decoded = Json::decode($contents);
        $service = ServiceTransformer::fromJson($decoded);

        $this->addMockResponse($fixture);
        $actual = $this->kong->postService($service);
        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertNotFalse($lastRequest, 'No request has been sent');

        $body = $lastRequest->getBody()->getContents();
        $this->assertJsonStringEqualsJsonFile($data, $body);
        $this->assertEquals(
            $actual,
            $service
        );
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
