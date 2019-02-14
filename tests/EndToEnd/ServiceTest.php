<?php

namespace Test\EndToEnd\KongClient;

use Psr\Http\Client\ClientExceptionInterface;
use TFarla\KongClient\Service;

class ServiceTest extends TestCase
{
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

    /** @test */
    public function itShouldGetServices()
    {
        $service = new Service();
        $service->setName('test2');
        $service->setHost('example.com');
        $service->setPort(80);
        $service->setProtocol('http');

        $actual = $this->kong->postService($service);
        $result = $this->kong->getServices();
        $this->assertCount(1, $result->getData());
        $this->assertEquals([$actual], $result->getData());
        $this->assertNull($result->getNext());
    }

    /**
     * @dataProvider serviceProvider
     * @test
     */
    public function itShouldCreateService(Service $service)
    {
        $actual = $this->kong->postService($service);
        if (!is_null($service->getUrl())) {
            $this->assertSame(
                $service->getUrl(),
                "{$actual->getProtocol()}://{$actual->getHost()}:{$actual->getPort()}{$actual->getPath()}"
            );
        }

        $this->assertSame($service->getName(), $actual->getName());
        $this->assertNotNull($actual->getReadTimeout());
        $this->assertNotNull($actual->getWriteTimeout());
        $this->assertNotNull($actual->getConnectTimeout());
    }

    /** @test */
    public function itShouldNotAllowEmptyService()
    {
        $this->expectException(ClientExceptionInterface::class);
        $this->expectExceptionMessageRegExp('/^schema violation/');
        $service = new Service();
        $this->kong->postService($service);
    }

    /** @test */
    public function itShouldDeleteService()
    {
        $this->expectException(ClientExceptionInterface::class);
        $service = new Service();
        $service->setName('test');
        $service->setUrl('http://example.com');

        $createdService = $this->kong->postService($service);
        $id = $createdService->getId();
        if (is_null($id)) {
            throw new \UnexpectedValueException('Id should not be null');
        }

        $this->kong->deleteService($id);
        $this->assertNull($this->kong->getService($id));
    }

    /**
     * @test
     */
    public function itShouldUpdateService()
    {
        $service = new Service();
        $service->setName('test');
        $service->setUrl('http://example.com');

        $createdService = $this->kong->postService($service);

        $createdService->setName('test2');
        $createdService->setPort(801);
        $createdService->setProtocol('http');
        $createdService->setPath('/test');
        $createdService->setReadTimeout(10);
        $createdService->setWriteTimeout(20);
        $createdService->setRetries(100);
        $createdService->setConnectTimeout(30);

        $updatedServices = $this->kong->putService($createdService);
        $createdService->setUpdatedAt($updatedServices->getUpdatedAt());
        $this->assertEquals($createdService, $updatedServices);
    }

    public function serviceProvider()
    {
        $serviceA = new Service();
        $serviceA->setName('test');
        $serviceA->setUrl('http://example.com:81/test');

        $serviceB = new Service();
        $serviceB->setName('testb');
        $serviceB->setProtocol('https');
        $serviceB->setHost('example.com');
        $serviceB->setPort(8000);

        return [
            'withUrl' => [$serviceA],
            'withoutUrl' => [$serviceB]
        ];
    }
}
