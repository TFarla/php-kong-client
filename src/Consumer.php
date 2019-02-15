<?php


namespace TFarla\KongClient;


class Consumer
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
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $customId;

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
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getCustomId(): ?string
    {
        return $this->customId;
    }

    /**
     * @param string|null $customId
     */
    public function setCustomId(?string $customId): void
    {
        $this->customId = $customId;
    }
}