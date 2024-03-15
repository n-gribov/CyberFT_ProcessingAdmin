<?php

use app\helpers\ViewHelper;
use app\models\participants\operator\Operator;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\participants\key\forms\CreateKeyForm */
/* @var $operators Operator[] */

$ownerDropDownItems = array_reduce(
    $operators,
    function (array $carry, Operator $operator) {
        $carry[$operator->member_name][$operator->operator_id] = "$operator->operator_name ($operator->role_name)";
        return $carry;
    },
    []
);

?>

<div class="container-fluid document">
    <div id="edit_info">

        <?php if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?= Html::encode($dbError) ?></div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin([
            'id' => 'data-form',
            'action' => Url::to('/key/create'),
            'fieldConfig' => [
                'inputOptions' => [
                    'class' => 'form-control form-control-sm',
                    'maxlength' => true,
                ]
            ],
        ]);
        ?>

        <div class="row">
            <div class="col-12">
                <?= $form
                    ->field($model, 'ownerId')
                    ->widget(
                        Select2::class,
                        array_merge(
                            [
                                'attribute' => 'ownerId',
                                'data' => $ownerDropDownItems,
                            ],
                            ViewHelper::defaultSelect2Config()
                        )
                    ) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'code')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?= $form
                    ->field($model, 'startDate')
                    ->widget(DatePicker::class, ViewHelper::defaultDatePickerConfig())
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form
                    ->field($model, 'endDate')
                    ->widget(DatePicker::class, ViewHelper::defaultDatePickerConfig())
                ?>
            </div>
        </div>

        <?= $form->field($model, 'keyBodyBase64')->hiddenInput()->label(false) ?>

        <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

$js = <<<JS
function getSwiftCodeByOperatorId(id) {
    if (!id){
        return null;
    }
    if (!window.gon.swiftCodesByOperatorId) {
        return null;
    }
    return window.gon.swiftCodesByOperatorId[id] || null;
}

function updateKeyCode(ownerId) {
    var swiftCode = getSwiftCodeByOperatorId(ownerId);
    if (!swiftCode) {
        return;
    }
    var input = $('#createkeyform-code');
    var newCode = input.val().replace(/^[a-z0-9@]+\-([a-z0-9]+)$/i, swiftCode + '-$1');
    input.val(newCode);
}

$('body').on('change', '#createkeyform-ownerid', function() {
    updateKeyCode($(this).val());
});
JS;

$this->registerJs($js, View::POS_READY);
