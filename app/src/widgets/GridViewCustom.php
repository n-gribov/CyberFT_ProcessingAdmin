<?php

namespace app\widgets;

use kop\y2sp\ScrollPager;
use Yii;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\helpers\Html;

class GridViewCustom extends \yii\grid\GridView
{
    public $tableOptions = ['class' => 'table table-striped table-bordered table-hover table-sm'];

    public $layout = "{items}\n{pager}";

    public $dataColumnClass = DataColumnCustom::class;

    public $filterInputOptions = ['class' => 'form-control form-control-sm'];

    public $beforePageRender;

    public function init()
    {
        parent::init();

        if ($this->dataProvider->pagination) {
            $this->dataProvider->pagination->pageSize = 50;
        }

        $this->pager = [
            'class' => ScrollPager::class,
            'container' => '.grid-view tbody',
            'item' => 'tr',
            'noneLeftText' => '',
            'triggerOffset' => 99999,
            'paginationSelector' => '.pagination',
            'spinnerTemplate' => Yii::$app->controller->renderPartial('@app/views/pager/_spinnerTemplate.php'),
            'triggerTemplate' => Yii::$app->controller->renderPartial('@app/views/pager/_triggerTemplate.php'),
            'eventOnRender' => $this->beforePageRender,
        ];
    }

    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                if ($column instanceof DataColumn) {
                    $column->filterInputOptions = array_merge($column->filterInputOptions, $this->filterInputOptions);
                }
                $cells[] = $column->renderFilterCell();
            }

            return Html::tag('tr', implode('', $cells), $this->filterRowOptions);
        }

        return '';
    }
}
