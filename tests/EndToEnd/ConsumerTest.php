<?php


namespace Test\EndToEnd\KongClient;


use TFarla\KongClient\Consumer;
use TFarla\KongClient\ConsumerPaginatedResult;

/**
 * Class ConsumerTest
 * @package Test\EndToEnd\KongClient
 */
class ConsumerTest extends TestCase
{
    /** @test */
    public function itShouldGetConsumers()
    {
        $result = $this->kong->getConsumers();
        $this->assertCount(0, $result->getData());
        $this->assertNull($result->getNext());
        $this->assertNull($result->getOffset());
    }

    /** @test */
    public function itShouldSupportPagination()
    {
        $consumers = [];
        for ($i = 0; $i < 10; $i++) {
            $consumer = new Consumer();
            $consumer->setUsername("test$i");
            $consumer->setCustomId("custom$i");
            $consumers[] = $this->kong->postConsumer($consumer);
        }

        $this->assertHasPaginationSupport($consumers, function ($size, $offset) {
            return $this->kong->getConsumers($size, $offset);
        });
    }

    /**
     * @param $items
     * @param callable $getItems
     */
    private function assertHasPaginationSupport($items, callable $getItems)
    {
        $offset = null;
        $actualItems = [];
        $size = 1;
        for ($i = 0; $i < count($items); $i++) {
            /** @var ConsumerPaginatedResult $result */
            $result = $getItems($size, $offset);
            $this->assertCount(1, $result->getData());
            $actualItems[] = $result->getData()[0];
            $offset = $result->getOffset();
        }

        sort($items);
        sort($actualItems);

        $this->assertEquals($items, $actualItems);
    }

    /**
     * @dataProvider consumerProvider
     * @test
     * @param Consumer $consumer
     * @throws \Http\Client\Exception
     */
    public function itShouldPostConsumer(Consumer $consumer)
    {
        $created = $this->kong->postConsumer($consumer);

        $consumer->setId($created->getId());
        $consumer->setCreatedAt($created->getCreatedAt());

        $this->assertNotNull($created->getId());
        $this->assertNotNull($created->getCreatedAt());
        $this->assertEquals($consumer, $created);
    }

    /**
     * @test
     * @dataProvider consumerProvider
     * @param Consumer $consumer
     * @throws \Http\Client\Exception
     */
    public function itShouldPutConsumer(Consumer $consumer)
    {
        $consumer = $this->kong->postConsumer($consumer);
        $consumer->setCustomId('updated-custom-id');
        $consumer->setUsername('updated-username');

        $updatedConsumer = $this->kong->putConsumer($consumer);
        $this->assertEquals($consumer, $updatedConsumer);
    }

    /**
     * @return array
     */
    public function consumerProvider()
    {
        $consumer = new Consumer();
        $consumer->setUsername('test');
        $consumer->setCustomId('test2');

        return [
            [$consumer]
        ];
    }
}
