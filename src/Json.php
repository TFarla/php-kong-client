<?php


namespace TFarla\KongClient;

/**
 * Class Json
 * @package TFarla\KongClient
 */
class Json
{
    /**
     * @param mixed $value
     * @return string
     */
    public static function encode($value): string
    {
        $encoded = json_encode($value);
        if ($encoded === false) {
            throw new \InvalidArgumentException('The provided value can not be serialized to json.');
        }

        return $encoded;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function decode($value)
    {
        $decoded = json_decode($value, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('The provided value could not be decoded');
        }

        return $decoded;
    }
}
