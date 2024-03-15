<?php

namespace app\services\documents;

use app\models\documents\document\Document;
use app\models\documents\document\DocumentQuery;
use Webmozart\Assert\Assert;
use Yii;

class ResendDocumentsService
{
    private int $documentsCountLimit;
    private DocumentQuery $query;

    public function __construct(DocumentQuery $query, int $documentsCountLimit = 1_000)
    {
        Assert::greaterThan($documentsCountLimit, 0);

        $this->query = $query;
        $this->documentsCountLimit = $documentsCountLimit;
    }

    public function resend(): ResendDocumentsResult
    {
        $this->ensureDocumentsCountLimitIsNotExceeded();

        $skippedCount = 0;
        $resentCount = 0;
        $failedCount = 0;
        foreach ($this->query->all() as $document) {
            /** @var Document $document */
            if (!$document->isResendable()) {
                $skippedCount++;
                continue;
            }
            Yii::info("Resending document {$document->message_id}");
            if ($document->resend()) {
                $resentCount++;
            } else {
                $failedCount++;
            }
        }

        return new ResendDocumentsResult($resentCount, $skippedCount, $failedCount);
    }

    private function ensureDocumentsCountLimitIsNotExceeded(): void
    {
        if ($this->query->count() > $this->documentsCountLimit) {
            throw new \DomainException(
                Yii::t(
                    'app/document',
                    'Number of documents being resent cannot exceed {limit}',
                    ['limit' => $this->documentsCountLimit]
                )
            );
        }
    }
}
