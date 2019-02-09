<?php


namespace TFarla\KongClient;

use TFarla\KongClient\Route\Target;

class Route
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var int|null
     */
    private $createdAt;

    /**
     * @var int|null
     */
    private $updateAt;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]
     */
    private $protocols = ['http', 'https'];

    /**
     * @var string[]|null
     */
    private $methods;

    /**
     * @var string[]|null
     */
    private $hosts;

    /**
     * @var string[]|null
     */
    private $paths;

    /**
     * @var int|null
     */
    private $regexPriority;

    /**
     * @var boolean
     */
    private $stripPath = true;

    /**
     * @var boolean
     */
    private $preserveHost = false;

    /**
     * @var string|null
     */
    private $serviceId;

    /**
     * @var string[]|null
     */
    private $snis;

    /**
     * @var Target[]|null
     */
    private $sources;

    /**
     * @var Target[]|null
     */
    private $destinations;

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
     * @return int|null
     */
    public function getUpdateAt(): ?int
    {
        return $this->updateAt;
    }

    /**
     * @param int|null $updateAt
     */
    public function setUpdateAt(?int $updateAt): void
    {
        $this->updateAt = $updateAt;
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
     * @return string[]
     */
    public function getProtocols(): array
    {
        return $this->protocols;
    }

    /**
     * @param string[] $protocols
     */
    public function setProtocols(array $protocols): void
    {
        $this->protocols = $protocols;
    }

    /**
     * @return string[]|null
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @param string[]|null $methods
     */
    public function setMethods(?array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return string[]|null
     */
    public function getHosts(): ?array
    {
        return $this->hosts;
    }

    /**
     * @param string[]|null $hosts
     */
    public function setHosts(?array $hosts): void
    {
        $this->hosts = $hosts;
    }

    /**
     * @return array|null
     */
    public function getPaths(): ?array
    {
        return $this->paths;
    }

    /**
     * @param string[]|null $paths
     */
    public function setPaths(?array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @return int|null
     */
    public function getRegexPriority(): ?int
    {
        return $this->regexPriority;
    }

    /**
     * @param int|null $regexPriority
     */
    public function setRegexPriority(?int $regexPriority): void
    {
        $this->regexPriority = $regexPriority;
    }

    /**
     * @return bool
     */
    public function isStripPath(): bool
    {
        return $this->stripPath;
    }

    /**
     * @param bool $stripPath
     */
    public function setStripPath(bool $stripPath): void
    {
        $this->stripPath = $stripPath;
    }

    /**
     * @return bool
     */
    public function isPreserveHost(): bool
    {
        return $this->preserveHost;
    }

    /**
     * @param bool $preserveHost
     */
    public function setPreserveHost(bool $preserveHost): void
    {
        $this->preserveHost = $preserveHost;
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
     * @return string[]|null
     */
    public function getSnis(): ?array
    {
        return $this->snis;
    }

    /**
     * @param string[]|null $snis
     */
    public function setSnis(?array $snis): void
    {
        $this->snis = $snis;
    }

    /**
     * @return Target[]|null
     */
    public function getSources(): ?array
    {
        return $this->sources;
    }

    /**
     * @param Target[]|null $sources
     */
    public function setSources(?array $sources): void
    {
        $this->sources = $sources;
    }

    /**
     * @return Target[]|null
     */
    public function getDestinations(): ?array
    {
        return $this->destinations;
    }

    /**
     * @param Target[]|null $destinations
     */
    public function setDestinations(?array $destinations): void
    {
        $this->destinations = $destinations;
    }
}
