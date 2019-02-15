<?php

namespace Test\Unit\KongClient;

use TFarla\KongClient\Json;
use TFarla\KongClient\Route\RouteTransformer;

class KongClientRouteTest extends CrudTestCase
{
    /**
     * @dataProvider routeFixtureProvider
     * @test
     * @param string $fixture
     * @throws \Http\Client\Exception
     */
    public function itShouldGetRoute(string $fixture)
    {
        $this->addMockResponse($fixture);
        $info = $this->readFixtureFromFile($fixture);
        $result = $this->kong->getRoute('test');
        $this->assertRequestHasBeenSent($this->mockClient);
        $this->assertJsonStringEqualsJsonFile(
            $info[0],
            Json::encode(RouteTransformer::toArray($result))
        );
    }

    /** @test */
    public function itShouldPostRoute()
    {
        $fixture = 'route.json';
        $this->addMockResponse($fixture);
        $fixtureInfo = $this->readFixtureFromFile($fixture);
        $route = RouteTransformer::fromArray($fixtureInfo[1]);
        $result = $this->kong->postRoute($route);

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertRequestHasBeenSent($this->mockClient);
        $this->assertEquals($route, $result);
        $this->assertSame('/routes', $lastRequest->getUri()->getPath());
        $this->assertEquals(
            Json::decode($lastRequest->getBody()->getContents()),
            RouteTransformer::toArray($result)
        );
    }

    /** @test */
    public function itShouldPutRoute()
    {
        $fixture = 'route.json';
        $this->addMockResponse($fixture);
        $fixtureInfo = $this->readFixtureFromFile($fixture);
        $route = RouteTransformer::fromArray($fixtureInfo[1]);
        $result = $this->kong->putRoute($route);

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertRequestHasBeenSent($this->mockClient);
        $this->assertEquals($route, $result);
        $this->assertSame('/routes/' . $route->getId(), $lastRequest->getUri()->getPath());
        $this->assertEquals(
            Json::decode($lastRequest->getBody()->getContents()),
            RouteTransformer::toArray($result)
        );
    }

    /** @test */
    public function itShouldOnlyPutRouteWheIdIsSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $fixture = 'route.json';
        $this->addMockResponse($fixture);
        $fixtureInfo = $this->readFixtureFromFile($fixture);
        $route = RouteTransformer::fromArray($fixtureInfo[1]);
        $route->setId(null);
        $this->kong->putRoute($route);
    }

    /** @test */
    public function itShouldDeleteRoute()
    {
        $id = 'test';
        $response = $this->requestFactory->createResponse(204);
        $this->mockClient->addResponse($response);
        $this->kong->deleteRoute($id);
        $this->assertRequestHasBeenSent($this->mockClient);
        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertSame('DELETE', $lastRequest->getMethod());
        $this->assertSame("/routes/$id", $lastRequest->getUri()->getPath());
    }

    public function routeFixtureProvider()
    {
        return [
            ['route.json'],
            ['route-tcp.json']
        ];
    }
}
