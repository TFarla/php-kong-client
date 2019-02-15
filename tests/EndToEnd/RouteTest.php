<?php

namespace Test\EndToEnd\KongClient;

use Psr\Http\Client\ClientExceptionInterface;
use TFarla\KongClient\Route;
use TFarla\KongClient\Service;

class RouteTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var string
     */
    private $serviceId;

    public function setUp(): void
    {
        parent::setUp();

        $service = new Service();
        $service->setName('test');
        $service->setUrl('http://example.com');
        $this->service = $this->kong->postService($service);
        if (is_null($this->service->getId())) {
            throw new \UnexpectedValueException('Service id should not be null');
        }

        $this->serviceId = $this->service->getId();
    }

    /** @test */
    public function itShouldSupportCursorBasedPagination()
    {
        $amountOfRoutes = 10;
        $routes = [];
        for ($i = 0; $i < $amountOfRoutes; $i++) {
            $route = new Route();
            $route->setPaths(['/']);
            $route->setServiceId($this->serviceId);

            $routes[] = $this->kong->postRoute($route);
        }

        $this->assertHasPaginationSupport($routes, function ($size, $offset) {
            return $this->kong->getRoutes($size, $offset);
        });
    }

    /** @test */
    public function itShouldPostRoute()
    {
        $route = new Route();
        $route->setName('test');
        $route->setPaths(['/', '/test']);
        $route->setServiceId($this->serviceId);

        $createdRoute = $this->kong->postRoute($route);

        $this->assertSame($route->getName(), $createdRoute->getName());
        $this->assertSame($route->getPaths(), $createdRoute->getPaths());
        $this->assertSame($route->getServiceId(), $createdRoute->getServiceId());
        $this->assertNull($route->getHosts());
        $this->assertNull($route->getDestinations());
        $this->assertNull($route->getSources());
    }

    /** @test */
    public function itShouldUpdateRoute()
    {
        $route = new Route();
        $route->setName('test');
        $route->setPaths(['/']);
        $route->setServiceId($this->serviceId);

        $createdRoute = $this->kong->postRoute($route);

        $createdRoute->setName('test2');
        $createdRoute->setPaths(null);
        $createdRoute->setProtocols(['tcp']);
        $createdRoute->setDestinations([
            new Route\Target(null, 80),
            new Route\Target('127.0.0.1', null)
        ]);

        $updatedRoute = $this->kong->putRoute($createdRoute);
        $createdRoute->setUpdateAt($updatedRoute->getUpdateAt());
        $createdRoute->setCreatedAt($updatedRoute->getCreatedAt());

        $this->assertEquals($createdRoute, $updatedRoute);
    }

    /** @test */
    public function itShouldGetRoute()
    {
        $route = new Route();
        $route->setName('test');
        $route->setPaths(['/', '/test']);
        $route->setServiceId($this->serviceId);

        $createdRoute = $this->kong->postRoute($route);
        $id = $this->getId($createdRoute);
        $actual = $this->kong->getRoute($id);
        $this->assertEquals($createdRoute, $actual);
    }

    /** @test */
    public function itShouldGetRoutes()
    {
        $route = new Route();
        $route->setName('test');
        $route->setPaths(['/', '/test']);
        $route->setServiceId($this->serviceId);

        $createdRoute = $this->kong->postRoute($route);
        $result = $this->kong->getRoutes();
        $this->assertEquals([$createdRoute], $result->getData());
        $this->assertNull($result->getNext());
    }

    /** @test */
    public function itShouldDeleteRoute()
    {
        $this->expectException(ClientExceptionInterface::class);
        $route = new Route();
        $route->setName('test');
        $route->setPaths(['/', '/test']);
        $route->setServiceId($this->serviceId);

        $createdRoute = $this->kong->postRoute($route);
        $id = $this->getId($createdRoute);
        $this->kong->deleteRoute($id);
        $this->kong->getRoute($id);
    }

    private function getId(Route $route)
    {
        if (is_null($route->getId())) {
            throw new \UnexpectedValueException('Route id should not be null');
        }

        return $route->getId();
    }
}
