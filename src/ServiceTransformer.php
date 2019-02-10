<?php


namespace TFarla\KongClient;

class ServiceTransformer
{
    public static function fromJson(array $rawService): Service
    {
        $service = new Service();
        $service->setId($rawService['id'] ?? null);
        $service->setName($rawService['name']);
        $service->setConnectTimeout($rawService['connect_timeout']);
        $service->setWriteTimeout($rawService['write_timeout']);
        $service->setReadTimeout($rawService['read_timeout']);
        $service->setHost($rawService['host']);
        $service->setPort($rawService['port']);
        $service->setProtocol($rawService['protocol']);
        $service->setRetries($rawService['retries']);
        $service->setPath($rawService['path']);
        $service->setCreatedAt($rawService['created_at']);
        $service->setUpdatedAt($rawService['updated_at']);

        return $service;
    }

    public static function toArray(Service $service): array
    {
        return [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'connect_timeout' => $service->getConnectTimeout(),
            'write_timeout' => $service->getWriteTimeout(),
            'read_timeout' => $service->getReadTimeout(),
            'host' => $service->getHost(),
            'port' => $service->getPort(),
            'protocol' => $service->getProtocol(),
            'retries' => $service->getRetries(),
            'path' => $service->getPath(),
            'created_at' => $service->getCreatedAt(),
            'updated_at' => $service->getUpdatedAt()
        ];
    }
}
