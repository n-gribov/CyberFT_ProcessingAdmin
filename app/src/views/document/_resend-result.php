<?php

use app\services\documents\ResendDocumentsResult;
use yii\web\View;

/** @var View $this */
/** @var ResendDocumentsResult $result */

?>

<div id="resend-results">
    <?= Yii::t('app/document', 'Processing result') ?>:
    <br>
    <ul>
        <li><?= Yii::t('app/document', 'resent') ?>: <?= $result->resentCount() ?></li>
        <li><?= Yii::t('app/document', 'skipped') ?>: <?= $result->skippedCount() ?></li>
        <li><?= Yii::t('app/document', 'failed to resend') ?>: <?= $result->failedCount() ?></li>
    </ul>
</div>

<?php

