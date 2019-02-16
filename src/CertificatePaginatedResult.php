<?php


namespace TFarla\KongClient;

class CertificatePaginatedResult extends PaginatedResult
{
    /** @return Certificate[] */
    public function getData(): array
    {
        return parent::getData();
    }
}
