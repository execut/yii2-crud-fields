<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\HasManyMultipleInput\Grid\Field;

use execut\crudFields\fields\HasManyMultipleInput\Grid\Field;
use execut\crudFields\Relation;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;

class DynaGrid extends Field
{
    public function render(Relation $relation, ActiveRecordInterface $model): ?string
    {
        $url = $relation->getUrl();
        $params = ArrayHelper::merge($this->getOptions($model), [
            'layout' => '<div class="dyna-grid-footer">{summary}{pager}<div class="dyna-grid-toolbar">{toolbar}</div></div>{items}',
            'toolbar' => [
                'dynaParams' => ['content' => '{dynagridFilter}{dynagridSort}{dynagrid}'],
            ],
            'bordered' => false,
            'updateUrl' => $url,
            'addButtonUrl' => $url,
            'containerOptions' => [
                'style' => <<<TEXT
overflow-x: scroll;
position: relative;
width: 100vw;
TEXT
            ],
            'floatHeader' => false,
            'resizableColumns' => false,
            'dataProvider' => $this->getDataProvider($model, $relation),
        ]);

        return \execut\actions\widgets\DynaGrid::widget([
            'dataProvider' => $params['dataProvider'],
            'columns' => $relation->getRelationModel()->getGridColumns(),
            'gridOptions' => $params,
        ]);
    }
}
