<?php


namespace TFarla\KongClient;

use TFarla\KongClient\Route\RouteTransformer;

class RoutePaginatedResult implements \JsonSerializable
{
    /**
     * @var Route[]
     */
    private $data = [];

    /**
     * @var string|null
     */
    private $next;

    /**
     * RoutePaginatedResult constructor.
     * @param array $data
     * @param string|null $next
     */
    public function __construct(array $data, ?string $next)
    {
        $this->data = $data;
        $this->next = $next;
    }

    /**
     * @return array
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
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $rawRoutes = [];
        foreach ($this->data as $route) {
            $rawRoutes[] = RouteTransformer::toArray($route);
        }

        return [
            'data' => $rawRoutes,
            'next' => $this->next
        ];
    }
}
