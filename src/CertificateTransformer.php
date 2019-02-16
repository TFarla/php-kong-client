<?php


namespace TFarla\KongClient;

/**
 * Class CertificateTransformer
 * @package TFarla\KongClient
 */
class CertificateTransformer
{
    /**
     * @param Certificate $certificate
     * @return array
     */
    public static function toRequestBody(Certificate $certificate): array
    {
        $requestBody = [
            'cert' => $certificate->getCert(),
            'key' => $certificate->getKey()
        ];

        return $requestBody;
    }

    /**
     * @param array $values
     * @return Certificate
     */
    public static function fromResponseBody(array $values): Certificate
    {
        $result = new Certificate($values['cert'], $values['key']);
        $result->setId($values['id']);
        $result->setCreatedAt($values['created_at']);

        return $result;
    }
}
