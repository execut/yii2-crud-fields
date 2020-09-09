<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\HasManyMultipleInput\GridRenderer\Params;

use execut\crudFields\fields\HasManyMultipleInput\GridRenderer\Params;
use execut\crudFields\Relation;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;

class Field implements Params
{
    protected string $relationName;
    protected array $columns;
    protected ActiveRecordInterface $model;
    public function __construct(string $relationName, array $columns, ActiveRecordInterface $model)
    {
        $this->relationName = $relationName;
        $this->columns = $columns;
        $this->model = $model;
    }

    public function toArray(): ?array
    {
        $relationName = $this->relationName;
        $dataProvider = new ActiveDataProvider([
            'query' => $this->model->getRelation($relationName),
        ]);

        $gridOptions = [
            'layout' => '{toolbar}{summary}{items}{pager}',
            'bordered' => false,
            'showOnEmpty' => true,
            'columns' => $this->columns,
            'dataProvider' => $dataProvider,
        ];

        return $gridOptions;
    }
}