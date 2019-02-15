<?php


namespace TFarla\KongClient;

class PluginPaginatedResult
{
    /**
     * @var Plugin[]
     */
    private $data = [];

    /**
     * @var string|null
     */
    private $next;

    /**
     * @var string|null
     */
    private $offset;

    /**
     * PluginPaginatedResult constructor.
     * @param Plugin[] $data
     * @param string|null $next
     * @param string|null $offset
     */
    public function __construct(array $data, ?string $next, ?string $offset)
    {
        $this->data = $data;
        $this->next = $next;
        $this->offset = $offset;
    }

    /**
     * @return Plugin[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getNext(): ?string
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
