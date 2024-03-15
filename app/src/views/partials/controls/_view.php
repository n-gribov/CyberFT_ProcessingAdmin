<?php

use yii\helpers\Html;

$addChildButtonUrl = $addChildButtonUrl ?? null;
$addChildButtonText = $addChildButtonText ?? null;

?>

<div class="d-flex mt-3">
    <div>
        <?= Html::a(
            '<i class="fas fa-edit"></i> ' . Yii::t('app', 'Update'),
            '#',
            [
                'id' => 'update-data',
                'class' => 'btn btn-primary btn-sm btn-block',
                'data' => ['id' => $model->primaryKey]
            ]
        ) ?>
    </div>
    <?php if ($addChildButtonUrl && $addChildButtonText): ?>
        <div class="pl-1">
            <?= Html::a(
                '<i class="fas fa-plus"></i> ' . $addChildButtonText,
                $addChildButtonUrl,
                ['class' => 'btn btn-primary btn-sm btn-block']
            ) ?>
        </div>
    <?php endif; ?>
    <div class="ml-auto">
        <?= Html::a('<i class="far fa-trash-alt"></i> ' . Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->primaryKey],
            [
                'class' => 'btn btn-danger btn-sm float-right',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>
</div>