<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use app\widgets\DetailViewCustom;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

?>

<div class="container-fluid document">
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= '<?= ' ?>DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
<?php
    if (($tableSchema = $generator->getTableSchema()) === false) {
        foreach ($generator->getColumnNames() as $name) {
            echo "                    '" . $name . "',\n";
        }
    } else {
        foreach ($generator->getTableSchema()->columns as $column) {
            $format = $generator->generateColumnFormat($column);
            echo "                    '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
?>                ],
            ]) ?>

            <?= '<?= ' ?>$this->renderFile('@app/views/partials/controls/_view.php', compact('model')) ?>
        </div>
    </div>
</div>
