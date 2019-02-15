<?php


namespace TFarla\KongClient;

class ServicePaginatedResult extends PaginatedResult
{
    /**
     * @return Service[]
     */
    public function getData(): array
    {
        return parent::getData();
    }
}
