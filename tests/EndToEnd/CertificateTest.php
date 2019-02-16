<?php


namespace Test\EndToEnd\KongClient;

use TFarla\KongClient\Certificate;

class CertificateTest extends TestCase
{
    /**
     * @test
     * @dataProvider certificateProvider
     * @param Certificate $certificate
     */
    public function itShouldPostCertificate(Certificate $certificate)
    {
        $created = $this->kong->postCertificate($certificate);

        $this->assertNotNull($created->getId());
        $this->assertNotNull($created->getCreatedAt());

        $certificate->setId($created->getId());
        $certificate->setCreatedAt($created->getCreatedAt());

        $this->assertEquals($certificate, $created);
    }

    /**
     * @test
     * @dataProvider certificateProvider
     * @param Certificate $certificate
     * @throws \Http\Client\Exception
     */
    public function itShouldGetCertificate(Certificate $certificate)
    {
        $created = $this->kong->postCertificate($certificate);
        $id = $created->getId();
        if (is_null($id)) {
            throw new \UnderflowException('id is not supposed to be null. Maybe the certificate was not created?');
        }

        $actual = $this->kong->getCertificate($id);
        $this->assertEquals($created, $actual);
    }

    /**
     * @test
     * @dataProvider certificateProvider
     * @param Certificate $certificate
     * @throws \Http\Client\Exception
     */
    public function itShouldPutCertificate(Certificate $certificate)
    {
        $created = $this->kong->postCertificate($certificate);
        $updated = clone $created;

        list ($key, $cert) = $this->getKeyPair('www2_example_com');
        $updated->setKey($key);
        $updated->setCert($cert);

        $id = $created->getId();
        if (is_null($id)) {
            throw new \UnexpectedValueException('id should not be null. Maybe the certificate was not created?');
        }

        $updated = $this->kong->putCertificate($updated);
        $this->assertEquals(
            $updated,
            $this->kong->getCertificate($id)
        );
    }

    /**
     * @test
     * @throws \Http\Client\Exception
     */
    public function itShouldGetCertificates()
    {
        $result = $this->kong->getCertificates();
        $this->assertCount(0, $result->getData());
        $this->assertNull($result->getOffset());
        $this->assertNull($result->getNext());
    }

    /**
     * @test
     * @dataProvider certificateProvider
     * @param Certificate $certificate
     * @throws \Http\Client\Exception
     */
    public function itShouldSupportPagination(Certificate $certificate)
    {
        $certificates = [];
        for ($i = 0; $i < 10; $i++) {
            $certificates[] = $this->kong->postCertificate($certificate);
        }

        $this->assertHasPaginationSupport($certificates, function ($size, $offset) {
            return $this->kong->getCertificates($size, $offset);
        });
    }

    private function getKeyPair(string $name)
    {
        $cert = file_get_contents($this->path(__DIR__, 'fixtures', 'certs', "$name.crt"));
        $key = file_get_contents($this->path(__DIR__, 'fixtures', 'certs', "$name.key"));

        return [$key, $cert];
    }

    public function certificateProvider()
    {
        list ($key, $cert) = $this->getKeyPair('www_example_com');

        $certificate = new Certificate($cert, $key);

        return [
            [$certificate]
        ];
    }

    private function path(string ...$pieces): string
    {
        return implode(DIRECTORY_SEPARATOR, $pieces);
    }
}
