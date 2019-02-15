<?php


namespace TFarla\KongClient;

class RoutePaginatedResult extends PaginatedResult
{
    /**
     * @return Route[]
     */
    public function getData(): array
    {
        return parent::getData();
    }
}
