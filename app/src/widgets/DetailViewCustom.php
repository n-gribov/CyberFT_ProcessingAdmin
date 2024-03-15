<?php

namespace app\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

class DetailViewCustom extends DetailView
{
    public $options = ['tag' => false];

    public $template = '<div class="row">'
        . '<div class="col-6"><p{captionOptions}>{label}:</p></div>'
        . '<div class="col-6"><p{contentOptions}>{value}</p></div>'
        . '</div>'
        . '<hr>';

    public $attributesGroups = [];

    public $groupHeaderTemplate = '<div class="row header-row {class}" data-toggle="collapse" data-target="{target}">
            <div class="col-12"><i class="fas fa-angle-down"></i><i class="fas fa-angle-right"></i>{header}</div>
        </div>';

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->attributesGroups)) {
            return parent::run();
        }

        $rows = [];
        foreach ($this->attributesGroups as $i => $group) {
            if (isset($group['visible']) && $group['visible'] === false) {
                continue;
            }
            $hasHeader = array_key_exists('header', $group) && $group['header'];
            $isCollapsed = array_key_exists('collapsed', $group) && $group['collapsed'];
            if ($hasHeader) {
                $dataBlockId = "data-block-$i";
                $rows[] = strtr(
                    $this->groupHeaderTemplate,
                    [
                        '{header}' => $group['header'],
                        '{target}' => "#$dataBlockId",
                        '{class}' => $isCollapsed ? 'collapsed' : '',
                    ]
                );
                $dataBlockClass = $isCollapsed ? 'collapse' : 'collapse show';
                $rows[] = "<div id='$dataBlockId' class='$dataBlockClass'>";
            }

            foreach ($group['attributes'] as $attributeId) {
                $attribute = $this->getAttribute($attributeId);
                if ($attribute) {
                    $rows[] = $this->renderAttribute($attribute, $i + 1);
                }
            }

            if ($hasHeader) {
                $rows[] = '</div>';
            }
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }

    protected function getAttribute($attributeId)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute['attribute'] === $attributeId) {
                return $attribute;
            }
        }

        return null;
    }
}
