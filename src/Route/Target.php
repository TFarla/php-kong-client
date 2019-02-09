<?php


namespace TFarla\KongClient\Route;

class Target
{
    /**
     * @var string|null
     */
    private $ip;

    /**
     * @var int|null
     */
    private $port;

    /**
     * Target constructor.
     * @param string|null $ip
     * @param int|null $port
     */
    public function __construct(?string $ip, ?int $port)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     */
    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
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
}
