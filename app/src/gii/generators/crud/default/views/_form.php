<?php

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use <?= ltrim($generator->modelClass, '\\') ?>;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$cancelRoute = Url::toRoute(['/<?= $generator->controllerID ?>/view', 'id' => $model->primaryKey]);

?>

<div class="container-fluid document">
    <div id="edit_info">

        <?= '<?php ' ?> if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?= '<?= ' ?> Html::encode($dbError) ?></div>
        <?= '<?php ' ?> endif; ?>

        <?= '<?php ' ?>$form = ActiveForm::begin([
            'id' => 'data-form',
            'fieldConfig' => [
                'inputOptions' => [
                    'class' => 'form-control form-control-sm',
                    'maxlength' => true,
                ]
            ],
        ]);
        ?>

<?php foreach ($generator->getColumnNames() as $attribute): ?>
        <div class="row">
            <div class="col-12">
                <?= '<?= ' ?>$form->field($model, '<?= $attribute ?>')->textInput() ?>
            </div>
        </div>
<?php endforeach; ?>

        <?= '<?php ' ?>if ($model->scenario === <?= $generator->modelClassWithoutNamespace ?>::SCENARIO_CREATE): ?>
            <?= '<?= ' ?>$this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?= '<?php ' ?>elseif ($model->scenario === <?= $generator->modelClassWithoutNamespace ?>::SCENARIO_UPDATE): ?>
            <?= '<?= ' ?>$this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>
        <?= '<?php ' ?>endif; ?>

        <?= '<?php ' ?>ActiveForm::end(); ?>
    </div>
</div>
