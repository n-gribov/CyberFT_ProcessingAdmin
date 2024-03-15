<?php

namespace app\services\documents;

class ResendDocumentsResult
{
    private int $resentCount;
    private int $skippedCount;
    private int $failedCount;

    public function __construct(int $resentCount, int $skippedCount, int $failedCount)
    {
        $this->resentCount = $resentCount;
        $this->skippedCount = $skippedCount;
        $this->failedCount = $failedCount;
    }

    public function resentCount(): int
    {
        return $this->resentCount;
    }

    public function skippedCount(): int
    {
        return $this->skippedCount;
    }

    public function failedCount(): int
    {
        return $this->failedCount;
    }
}
