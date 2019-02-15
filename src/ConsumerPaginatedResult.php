<?php


namespace TFarla\KongClient;

class ConsumerPaginatedResult extends PaginatedResult
{
    /**
     * @return Consumer[]
     */
    public function getData(): array
    {
        return parent::getData();
    }
}
