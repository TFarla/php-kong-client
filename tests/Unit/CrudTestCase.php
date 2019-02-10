<?php


namespace Test\Unit\KongClient;

use Http\Mock\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use TFarla\KongClient\Json;
use TFarla\KongClient\KongClient;

class CrudTestCase extends TestCase
{
    /**
     * @var Client
     */
    protected $mockClient;
    /**
     * @var Psr17Factory
     */
    protected $requestFactory;
    /**
     * @var KongClient
     */
    protected $kong;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockClient = new Client();
        $this->requestFactory = new Psr17Factory();
        $this->kong = new KongClient($this->mockClient, $this->requestFactory, $this->requestFactory);
    }

    public function fixture($name): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $name]);
    }

    /**
     * @param string $fixture
     */
    protected function addMockResponse(string $fixture): void
    {
        $fixture = $this->fixture($fixture);
        $body = $this->requestFactory->createStreamFromFile($fixture);
        $response = $this->requestFactory->createResponse()->withBody($body);
        $this->mockClient->addResponse($response);
    }

    protected function assertRequestHasBeenSent(Client $client)
    {
        $this->assertNotFalse($client->getLastRequest(), 'No request has been sent');
    }

    /**
     * @param string $fixture
     * @return array
     * @throws \Exception
     */
    protected function readFixtureFromFile(string $fixture): array
    {
        $fixturePath = $this->fixture($fixture);
        $contents = file_get_contents($fixturePath);
        if (!$contents) {
            throw new \Exception('Failed getting contents');
        }

        $decoded = Json::decode($contents);
        return [$fixturePath, $decoded];
    }
}
