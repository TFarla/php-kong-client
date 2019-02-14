<?php


namespace TFarla\KongClient;

class ServicePaginatedResult
{
    /**
     * @var Service[]
     */
    private $data = [];

    /**
     * @var string|null
     */
    private $next = null;

    /**
     * @var string|null
     */
    private $offset = null;

    /**
     * ServicePaginatedResult constructor.
     * @param Service[] $data
     * @param string|null $next
     * @param string|null $offset
     */
    public function __construct(array $data, ?string $next = null, ?string $offset = null)
    {
        $this->data = $data;
        $this->next = $next;
        $this->offset = $offset;
    }

    /**
     * @return Service[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return string|null
     */
    public function getOffset(): ?string
    {
        return $this->offset;
    }
}
