<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\HasManyMultipleInput\Grid;

use execut\crudFields\Relation;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

class Field implements Grid
{
    protected $options;
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function render(Relation $relation, ActiveRecordInterface $model): ?string
    {
        $dataProvider = $this->getDataProvider($model, $relation);
        if ($dataProvider === null) {
            return null;
        }

        $gridOptions = ArrayHelper::merge($this->getGridOptions(), [
            'columns' => $this->getColumns($relation),
            'dataProvider' => $dataProvider,
        ]);

        $options = $this->getOptions($model);

        $gridOptions = ArrayHelper::merge($gridOptions, $options);
        $widgetClass = \kartik\grid\GridView::class;
        if (!empty($gridOptions['class'])) {
            $widgetClass = $gridOptions['class'];
        }

        return $widgetClass::widget($gridOptions);
    }

    protected function getGridOptions()
    {
        return [
            'layout' => '{summary}{items}{pager}',
            'bordered' => false,
            'showOnEmpty' => true,
        ];
    }

    protected function getDataProvider(ActiveRecordInterface $model, Relation $relation): ?DataProviderInterface
    {
        $relationName = $relation->getName();
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getRelation($relationName),
        ]);

        return $dataProvider;
    }

    /**
     * getOptions
     * @param ActiveRecordInterface $model
     * @return mixed
     */
    protected function getOptions(ActiveRecordInterface $model): array
    {
        if (is_callable($this->options)) {
            $options = call_user_func_array($this->options, [$model]);
        } else {
            $options = $this->options;
        }
        return $options;
    }

    /**
     * getColumns
     * @param Relation $relation
     * @return mixed
     * @throws \yii\db\Exception
     */
    protected function getColumns(Relation $relation)
    {
        return $relation->getRelationModel()->getGridColumns();
    }
}
