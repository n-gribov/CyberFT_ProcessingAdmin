<?php

namespace app\models;

class X509Certificate
{
    private $body;
    private $fingerprint;
    private $validFrom;
    private $validTo;

    public function __construct($certificateBody)
    {
        try {
            $this->loadCertificate($certificateBody);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException("Invalid certificate provided, caused by: {$exception->getMessage()}");
        }
    }

    private function loadCertificate($certificateBody)
    {
        $pemCertificateBody = $this->normalizeCertificate($certificateBody);
        $resource = openssl_x509_read($pemCertificateBody);
        $this->fingerprint = openssl_x509_fingerprint($resource, 'sha1', false);
        $certData = openssl_x509_parse($resource);
        $this->validFrom = isset($certData['validFrom']) ? $this->parseDate($certData['validFrom']) : null;
        $this->validTo = isset($certData['validTo']) ? $this->parseDate($certData['validTo']) : null;
        $this->body = $pemCertificateBody;
    }

    private function normalizeCertificate($certificateBody)
    {
        if ($this->isPem($certificateBody)) {
            return $certificateBody;
        }
        return $this->convertDerToPem($certificateBody);
    }

    private function isPem($certificateBody)
    {
        return strpos($certificateBody, '-----BEGIN CERTIFICATE-----') === 0;
    }

    private function convertDerToPem($derData)
    {
        $pem = chunk_split(base64_encode($derData), 64, "\n");
        return "-----BEGIN CERTIFICATE-----\n$pem-----END CERTIFICATE-----\n";
    }

    private function parseDate($value)
    {
        $format = strlen($value) > 13 ? 'YmdHis?' : 'ymdHis?'; // e.g. 170906130457Z or 20570827130457Z
        $dateTime = \DateTime::createFromFormat($format, $value);

        return $dateTime === false ? null : $dateTime;
    }

    /**
     * @return string|null
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidTo()
    {
        return $this->validTo;
    }
}
