<?php

namespace Test\KongClient;

use TFarla\KongClient\Json;
use TFarla\KongClient\ServiceTransformer;

class KongClientTest extends CrudTestCase
{
    /**
     * @dataProvider servicesFixtureProvider
     * @test
     * @param string $fixture
     * @throws \Http\Client\Exception
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
        $this->assertSame("/services/$serviceName", $lastRequest->getUri()->getPath());
    }

    /**
     * @dataProvider mutationProvider
     * @test
     */
    public function itShouldMutateService(string $method)
    {
        $fixture = 'service.json';
        list($fixturePath, $decoded) = $this->readFixtureFromFile($fixture);
        $service = ServiceTransformer::fromJson($decoded);
        $this->addMockResponse($fixture);
        $actual = $this->kong->{$method . 'Service'}($service);

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertNotFalse($lastRequest, 'No request has been sent');
        $this->assertSame($method, $lastRequest->getMethod());

        $uri = '/services';
        if ($method !== 'POST') {
            $uri .= "/{$service->getId()}";
        }

        $this->assertSame(
            $uri,
            $lastRequest->getUri()->getPath()
        );

        $body = $lastRequest->getBody()->getContents();
        $this->assertJsonStringEqualsJsonFile($fixturePath, $body);
        $this->assertEquals($service, $actual);
    }

    public function mutationProvider()
    {
        return [
            ['POST'],
            ['PUT']
        ];
    }

    /** @test */
    public function itShouldOnlyPutWhenTheServiceHasAnId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $fixture = 'service.json';
        $data = $this->readFixtureFromFile($fixture);
        $service = ServiceTransformer::fromJson($data[1]);
        $service->setId(null);
        $this->kong->putService($service);
    }

    /** @test */
    public function itShouldDeleteService()
    {
        $id = 'test';
        $response = $this->requestFactory->createResponse(204);
        $this->mockClient->addResponse($response);
        $this->kong->deleteService($id);
        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertRequestHasBeenSent($this->mockClient);
        $this->assertSame('DELETE', $lastRequest->getMethod());
        $this->assertSame("/services/$id", $lastRequest->getUri()->getPath());
    }

    public function servicesFixtureProvider()
    {
        return [
            ['services-empty.json'],
            ['services.json']
        ];
    }
}
