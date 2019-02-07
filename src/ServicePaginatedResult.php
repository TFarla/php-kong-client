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
     * ServicePaginatedResult constructor.
     * @param Service[] $data
     * @param string|null $next
     */
    public function __construct(array $data, ?string $next = null)
    {
        $this->data = $data;
        $this->next = $next;
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
     * @param array $anArray
     * @return ServicePaginatedResult
     */
    public static function __set_state(array $anArray)
    {
        $obj = new self($anArray['data'], $anArray['next']);

        return $obj;
    }
}
