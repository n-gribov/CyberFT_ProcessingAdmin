<?php

use yii\widgets\ActiveForm;

/** @var \yii\web\View $this */
/** @var \yii\db\ActiveRecord $searchModel */
/** @var string $queryAttribute */
/** @var string $searchUrl */

$formConfig = [
    'method' => 'get',
    'action' => $searchUrl,
    'fieldConfig' => [
        'inputOptions' => [
            'class' => 'form-control form-control-sm',
        ]
    ],
];

$js = <<<JS
    $('body').on('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && (event.key === 'F' || event.key === 'А')) {
            $('#journal-search-block input')
                .trigger('focus')
                .select();
        }
    });
JS;

$this->registerJs($js)

?>

<div id="journal-search-block">
    <?php $form = ActiveForm::begin($formConfig) ?>
        <i class="fas fa-search"></i>
        <?= $form->field($searchModel, $queryAttribute)->label(false) ?>
    <?php ActiveForm::end(); ?>
</div>
