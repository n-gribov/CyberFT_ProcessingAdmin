<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$modelClass = StringHelper::basename($generator->modelClass);
$modelName = Inflector::camel2words($modelClass, false);

echo "<?php\n";
?>

use app\widgets\GridViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('<?= $generator->messageCategory ?>', '<?= Inflector::pluralize(ucfirst($modelName)) ?>');

?>

<h2><?= '<?= ' ?>Html::encode($this->title) ?></h2>

<div class="filtrs">
    <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name='<?= $generator->controllerID ?>' href="#">
        <i class="fas fa-plus"></i> <?= "<?= Yii::t('" . $generator->messageCategory . "', 'Create " . $modelName . "') ?>\n" ?>
    </a>
</div>

<?= '<?= ' ?>GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['view', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    <?= "'columns' => [\n" ?>
<?php
    $count = 0;
    if (($tableSchema = $generator->getTableSchema()) === false) {
        foreach ($generator->getColumnNames() as $name) {
            if (++$count < 6) {
                echo "        '" . $name . "',\n";
            } else {
                echo "//        '" . $name . "',\n";
            }
        }
    } else {
        foreach ($tableSchema->columns as $column) {
            $format = $generator->generateColumnFormat($column);
            if (++$count < 6) {
                echo "        '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            } else {
                echo "//        '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            }
        }
    }
?>
    ],
]); ?>

<?= '<?= ' ?>$this->renderFile('@app/views/partials/_modal.php') ?>
