<?php


namespace TFarla\KongClient;

class Plugin
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $name;

    /** @var int|null */
    private $createdAt;

    /** @var array */
    private $config = [];

    /** @var string */
    private $runOn = 'first';

    /** @var boolean */
    private $enabled = true;

    /** @var string|null */
    private $serviceId = null;

    /** @var string|null */
    private $routeId = null;

    /** @var string|null */
    private $consumerId = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @param int|null $createdAt
     */
    public function setCreatedAt(?int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getRunOn(): string
    {
        return $this->runOn;
    }

    /**
     * @param string $runOn
     */
    public function setRunOn(string $runOn): void
    {
        $this->runOn = $runOn;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string|null
     */
    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    /**
     * @param string|null $serviceId
     */
    public function setServiceId(?string $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return string|null
     */
    public function getRouteId(): ?string
    {
        return $this->routeId;
    }

    /**
     * @param string|null $routeId
     */
    public function setRouteId(?string $routeId): void
    {
        $this->routeId = $routeId;
    }

    /**
     * @return string|null
     */
    public function getConsumerId(): ?string
    {
        return $this->consumerId;
    }

    /**
     * @param string|null $consumerId
     */
    public function setConsumerId(?string $consumerId): void
    {
        $this->consumerId = $consumerId;
    }
}
