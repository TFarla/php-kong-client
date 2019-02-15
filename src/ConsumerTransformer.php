<?php


namespace TFarla\KongClient;

/**
 * Class ConsumerTransformer
 * @package TFarla\KongClient
 */
class ConsumerTransformer
{
    /**
     * @param array $rawConsumer
     * @return Consumer
     */
    public static function fromResponseBody(array $rawConsumer): Consumer
    {
        $consumer = new Consumer();
        $consumer->setId($rawConsumer['id']);
        $consumer->setUsername($rawConsumer['username']);
        $consumer->setCustomId($rawConsumer['custom_id']);
        $consumer->setCreatedAt($rawConsumer['created_at']);

        return $consumer;
    }

    /**
     * @param Consumer $consumer
     * @return array
     */
    public static function toRequest(Consumer $consumer): array
    {
        $requestBody = [];
        if (!is_null($consumer->getUsername())) {
            $requestBody['username'] = $consumer->getUsername();
        }

        if (!is_null($consumer->getCustomId())) {
            $requestBody['custom_id'] = $consumer->getCustomId();
        }

        return $requestBody;
    }
}
