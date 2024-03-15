<?php

use app\helpers\ViewHelper;
use app\models\documents\document\Document;
use app\models\documents\document\DocumentSearch;
use app\widgets\GridViewCustom;
use app\widgets\JournalSearchBox;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\grid\CheckboxColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array */
/* @var $types \app\models\documents\documentType\DocumentType[] */
/* @var $exportUrl string */

$this->title = Yii::t('app/document', 'Documents');

$typeDropDownListItems = ArrayHelper::map($types, 'message_code', 'message_code');

?>
<div class="document-index">
    <h2><?= Html::encode($this->title) ?></h2>

    <ul class="nav sub-menu justify-content-end">
        <li class="nav-item">

            <!-- add fields -->
<!--            <a class="nav-link btn btn-light" data-toggle="dropdown" href="#" aria-expanded="false">-->
<!--                <i class="fas fa-tasks"></i> --><?//= Yii::t('app/document', 'Fields') ?>
<!--            </a>-->
            <div class="dropdown-menu shadow-lg" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(1082px, 56px, 0px);">
                <?php $document = new Document(); ?>

                <form class="px-4 py-3" action="">
                    <h5><?= Yii::t('app', 'Setup fields') ?></h5>
                    <?php foreach ($document->attributeLabels() as $attribute => $label): ?>
                        <div class="form-check">
                            <label class="form-check-label" onclick="noClose(event)">
                                <input class="form-check-input" type="checkbox" value="<?= Html::encode($attribute) ?>"> <?= Html::encode($label) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </form>

            </div>
            <!-- add fields -->

        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-light" href="<?= $exportUrl ?>">
                <i class="far fa-file-excel"></i> <?= Yii::t('app/document', 'Export') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-light" href="#" onclick="location.reload(); return false;">
                <i class="fas fa-sync-alt"></i> <?= Yii::t('app/document', 'Refresh') ?></a>
        </li>
    </ul>

    <div class="filtrs d-flex justify-content-between">
        <div>
            <?= Html::button(
                '<i class="fa fa-paper-plane"></i> ' . Yii::t('app/document', 'Resend selected'),
                [
                    'id' => 'resend-button',
                    'class' => 'btn btn-primary btn-sm',
                    'disabled' => true,
                ]
            );
            ?>
        </div>
        <?= JournalSearchBox::widget(['searchModel' => $searchModel, 'searchUrl' => '/document']) ?>
    </div>

    <?= GridViewCustom::widget([
        'id' => 'documents-table',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'beforePageRender' => new JsExpression('function (items) { beforePageRender(items); }'),
        'columns' => [
            [
                'class' => CheckboxColumn::class,
                'checkboxOptions' => function (Document $model) {
                    $options = [];
                    $options['disabled'] = !$model->isResendable();
                    return $options;
                }
            ],
            'message_id',
            [
                'attribute' => 'message_code',
                'label' => Yii::t('app/document', 'Message type'),
                'filter' => Html::button(
                        Yii::t('app', 'Select') . '...',
                        [
                            'class' => 'btn btn-filter',
                            'data'  => [
                                'toggle' => 'modal',
                                'target' => '#type-filter-modal',
                            ],
                        ]
                    )
                    . Html::activeHiddenInput(
                        $searchModel,
                        'message_code_selection_id',
                    ),
            ],
            [
                'attribute' => 'snd_full_swift_code',
                'label' => Yii::t('app/document', 'Sender code'),
            ],
            'sender_name',
            [
                'attribute' => 'rsv_full_swift_code',
                'label' => Yii::t('app/document', 'Receiver code'),
            ],
            'receiver_name',
            [
                'attribute' => 'sender_msg_code',
                'label' => Yii::t('app/document', 'Message identifier'),
            ],
            [
                'attribute' => 'send_status_name',
                'value' => function (Document $document) {
                    return Yii::t('app/document-status', $document->send_status_name);
                },
                'filter' => Select2::widget(
                    array_merge(
                        [
                            'model' => $searchModel,
                            'attribute' => 'send_status',
                            'data' => $statuses,
                        ],
                        ViewHelper::defaultSelect2Config()
                    )
                ),
            ],
            [
                'attribute' => 'reg_time',
                'format' => 'datetime',
                'filter' => DateRangePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'reg_time_range',
                    'options'       => [
                        'autocomplete' => 'off',
                        'class'        => 'form-control form-control-sm',
                        'style'        => 'min-width: 135px;'
                    ],
                    'pluginOptions' => [
                        'locale'      => [
                            'format' => 'DD.MM.YYYY',
                            'cancelLabel' => Yii::t('app', 'Clear'),
                        ],
                    ],
                    'pluginEvents'=>[
                        'cancel.daterangepicker' => "function (e, picker) {
                            var input = \$('#documentsearch-reg_time_range');
                            var prevVal = input.val();
                            input.val('');
                            if (prevVal != '') {
                                input.trigger('change');
                            }
                        }",
                    ],
                ]),
            ],
            [
                'attribute' => 'final_time',
                'format' => 'datetime',
                'filter' => DatePicker::widget(
                    array_merge(
                        [
                            'model' => $searchModel,
                            'attribute' => 'final_time',
                        ],
                        ViewHelper::defaultDatePickerConfig()
                    )
                ),
            ],
            'message_cnt',
        ],
    ]); ?>
</div>

<form action="/document/batch-resend-by-ids" id="resend-by-ids-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
</form>
<form action="/document/batch-resend-by-search-params" id="resend-by-search-params-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
</form>

<?php
echo $this->renderFile('@app/views/partials/_modal.php');
echo $this->renderFile(
    '@app/views/document/_type-filter-modal.php',
    [
        'data'        => $typeDropDownListItems,
        'searchModel' => $searchModel,
    ]
);

$this->registerJsFile('/js/document/index.js', ['depends' => JqueryAsset::class]);
$this->registerCssFile('/css/document/index.css');
