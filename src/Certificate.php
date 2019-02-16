<?php


namespace TFarla\KongClient;

class Certificate
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
     * @var string
     */
    private $cert;

    /**
     * @var string
     */
    private $key;

    /**
     * Certificate constructor.
     * @param string $cert
     * @param string $key
     */
    public function __construct(string $cert, string $key)
    {
        $this->cert = $cert;
        $this->key = $key;
    }

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
     * @return string
     */
    public function getCert(): string
    {
        return $this->cert;
    }

    /**
     * @param string $cert
     */
    public function setCert(string $cert): void
    {
        $this->cert = $cert;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}
