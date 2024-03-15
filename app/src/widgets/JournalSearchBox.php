<?php

namespace app\widgets;

use yii\base\Widget;

class JournalSearchBox extends Widget
{
    public $searchModel;
    public $queryAttribute = 'query';
    public $searchUrl;

    public function run()
    {
        return $this->render(
            'journal-search-box',
            [
                'searchModel' => $this->searchModel,
                'queryAttribute' => $this->queryAttribute,
                'searchUrl' => $this->searchUrl,
            ]
        );
    }
}
