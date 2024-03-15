<?php

namespace models;

use app\models\X509Certificate;

class X509CertificateTest extends \Codeception\Test\Unit
{
    protected $tester;

    /**
     * @dataProvider certificateDataProvider
     */
    public function testCreateFromValidCertificate($certificateBody, $fingerPrint, $valifFrom, $validTo)
    {
        $certificate = new X509Certificate($certificateBody);

        $this->assertEquals($fingerPrint, $certificate->getFingerprint());
        $this->assertEquals($valifFrom, $certificate->getValidFrom()->format('Y-m-d'));
        $this->assertEquals($validTo, $certificate->getValidTo()->format('Y-m-d'));
    }

    public function testCreateFromInvalidCertificate()
    {
        $this->expectException(\InvalidArgumentException::class);
        new X509Certificate('jahsdhasdlhasjkdfh');
    }

    public function certificateDataProvider()
    {
        return [
            [
                'certificate' => $this->getDataFileContent('x509/pem.cer'),
                'fingerprint' => 'f415a05e3d92b05e971e64445bde74420f21c159',
                'validFrom' => '2018-08-01',
                'validTo' => '2018-11-01',
            ],
            [
                'certificate' => $this->getDataFileContent('x509/der.cer'),
                'fingerprint' => '542ff06bd1cf063cc0e580f1d8a1f707d34de5a9',
                'validFrom' => '2018-08-01',
                'validTo' => '2018-11-01',
            ],
        ];
    }

    private function getDataFileContent($relativePath)
    {
        $path = codecept_data_dir() . $relativePath;
        return file_get_contents($path);
    }
}
