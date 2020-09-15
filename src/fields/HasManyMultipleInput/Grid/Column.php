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
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

class Column extends Field
{
    public function getGridOptions()
    {
        return [
            'layout' => '{items}',
            'export' => false,
            'resizableColumns' => false,
            'bordered' => false,
            'toolbar' => '',
            'showOnEmpty' => true,
        ];
    }

    protected function getColumns(Relation $relation)
    {
        $columns = parent::getColumns($relation);
        unset($columns['actions']);

        return $columns;
    }

    protected function getDataProvider(ActiveRecordInterface $model, Relation $relation): ?DataProviderInterface
    {
        $relationName = $relation->getName();
        if ($model->isRelationPopulated($relationName)) {
            $allModels = $model->{$relationName};
        } else {
            $limit = $relation->getColumnRecordsLimit();
            $q = $model->getRelation($relationName);
            if ($limit !== false) {
                if ($limit === null) {
                    $limit = 10;
                }

                $q->limit($limit);
            }

            $allModels = $q->all();
        }

        if (!$allModels) {
            return null;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
        ]);

        return $dataProvider;
    }
}
