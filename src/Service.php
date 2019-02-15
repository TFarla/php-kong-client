<?php


namespace TFarla\KongClient;

class Service
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
    private $updatedAt;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var int|null
     */
    private $retries;
    /**
     * @var string|null
     */
    private $url;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string|null
     */
    private $protocol;
    /**
     * @var string|null
     */
    private $host;
    /**
     * @var int|null
     */
    private $port;
    /**
     * @var int|null
     */
    private $connectTimeout;
    /**
     * @var int|null
     */
    private $writeTimeout;
    /**
     * @var int|null
     */
    private $readTimeout;

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
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * @param int|null $updatedAt
     */
    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
    public function getRetries(): ?int
    {
        return $this->retries;
    }

    /**
     * @param int|null $retries
     */
    public function setRetries(?int $retries): void
    {
        $this->retries = $retries;
    }

    /**
     * @return string|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @param string|null $protocol
     */
    public function setProtocol(?string $protocol): void
    {
        $this->protocol = $protocol;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     */
    public function setPort(?int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return int|null
     */
    public function getConnectTimeout(): ?int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int|null $connectTimeout
     */
    public function setConnectTimeout(?int $connectTimeout): void
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * @return int|null
     */
    public function getWriteTimeout(): ?int
    {
        return $this->writeTimeout;
    }

    /**
     * @param int|null $writeTimeout
     */
    public function setWriteTimeout(?int $writeTimeout): void
    {
        $this->writeTimeout = $writeTimeout;
    }

    /**
     * @return int|null
     */
    public function getReadTimeout(): ?int
    {
        return $this->readTimeout;
    }

    /**
     * @param int|null $readTimeout
     */
    public function setReadTimeout(?int $readTimeout): void
    {
        $this->readTimeout = $readTimeout;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }
}
