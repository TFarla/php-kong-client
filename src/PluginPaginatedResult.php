<?php


namespace TFarla\KongClient;

class PluginPaginatedResult extends PaginatedResult
{
    /**
     * @return Plugin[]
     */
    public function getData(): array
    {
        return parent::getData();
    }
}
