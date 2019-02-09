<?php

namespace Test\KongClient;

use TFarla\KongClient\Json;
use TFarla\KongClient\Route\RouteTransformer;

class KongClientRouteTest extends CrudTestCase
{
    /** @test */
    public function itShouldGetRoutes()
    {
        $fixture = 'routes.json';
        $this->addMockResponse($fixture);
        $fixtureInfo = $this->readFixtureFromFile($fixture);

        $result = $this->kong->getRoutes();
        $this->assertRequestHasBeenSent($this->mockClient);

        $this->assertJsonStringEqualsJsonFile(
            $fixtureInfo[0],
            Json::encode($result)
        );
    }

    /**
     * @group ez
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
        $this->assertJsonStringEqualsJsonFile($info[0], Json::encode(RouteTransformer::toArray($result)));
    }

    public function routeFixtureProvider()
    {
        return [
            ['route.json'],
            ['route-tcp.json']
        ];
    }
}
