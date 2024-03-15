<?php

namespace app\widgets;

use yii\grid\DataColumn;
use yii\helpers\Html;

class DataColumnCustom extends DataColumn
{
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || $this->label === null && $this->attribute === null) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->attribute !== null && $this->enableSorting &&
            ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            $label = $this->addSortIconToLabel($label);
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
        }

        return $label;
    }

    protected function addSortIconToLabel($label)
    {
        $sortIcon = Html::tag('i', '', ['class' => 'fas fa-sort']);
        return "{$sortIcon} {$label}";
    }
}
