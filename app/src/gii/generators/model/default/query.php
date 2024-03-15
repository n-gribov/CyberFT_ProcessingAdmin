<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

use <?= $generator->queryBaseClass ?>;

class <?= $className ?> extends <?= $generator->queryBaseClassWithoutNamespace . "\n" ?>
{
    public function notDeleted()
    {
        return $this->andWhere(['not', ['status' => 0]]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
