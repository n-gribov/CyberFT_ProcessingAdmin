<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$modelPrimaryKeys = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$modelName = Inflector::camel2words($modelClass, false);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use yii\web\NotFoundHttpException;
use yii\web\Response;

class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $parent['verbs']['actions'] = [
            'delete' => ['POST'],
        ];

        return $parent;
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['view', 'create', 'update'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('<?= $generator->messageCategory ?>', '<?= ucfirst($modelName) ?>');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();
        $model->scenario = <?= $modelClass ?>::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('<?= $generator->messageCategory ?>', '<?= ucfirst($modelName) ?> is created'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('<?= $generator->messageCategory ?>', 'New <?= $modelName ?>');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = <?= $modelClass ?>::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('<?= $generator->messageCategory ?>', '<?= ucfirst($modelName) ?> is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('<?= $generator->messageCategory ?>', 'Update <?= $modelName ?>');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('<?= $generator->messageCategory ?>', '<?= ucfirst($modelName) ?> is deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function findModel(<?= $actionParams ?>)
    {
<?php
    if (count($modelPrimaryKeys) === 1) {
        $condition = '$id';
    } else {
        $condition = [];
        foreach ($modelPrimaryKeys as $pk) {
            $condition[] = "'$pk' => \$$pk";
        }
        $condition = '[' . implode(', ', $condition) . ']';
    }
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function makeModalData($model, $title, $contentView, $viewData = [])
    {
        $content = $this->renderAjax(
            $contentView,
            array_merge($viewData, ['model' => $model])
        );

        return compact('content', 'title');
    }

    private function makeFromData()
    {
        return [];
    }
}
