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
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecordInterface;

class Column implements Params
{
    protected string $relationName;
    protected array $columns;
    protected ActiveRecordInterface $model;
    protected $columnRecordsLimit;
    public function __construct(string $relationName, array $columns, ActiveRecordInterface $model, $columnRecordsLimit = null)
    {
        $this->relationName = $relationName;
        $this->columns = $columns;
        $this->model = $model;
        $this->columnRecordsLimit = $columnRecordsLimit;
    }

    public function toArray(): ?array
    {
        $dataProvider = $this->getDataProvider();
        if (!$dataProvider) {
            return null;
        }

        $gridColumns = $this->columns;
        unset($gridColumns['actions']);
        $gridOptions = [
            'layout' => '{items}',
            'export' => false,
            'resizableColumns' => false,
            'bordered' => false,
            'toolbar' => '',
            'showOnEmpty' => true,
            'columns' => $gridColumns,
            'dataProvider' => $dataProvider,
        ];

        return $gridOptions;
    }

    protected function getDataProvider()
    {
        if ($this->model->isRelationPopulated($this->relationName)) {
            $allModels = $this->model->{$this->relationName};
        } else {
            $limit = $this->columnRecordsLimit;
            $q = $this->model->getRelation($this->relationName);
            if ($limit !== false) {
                if ($limit === null) {
                    $limit = 10;
                }

                $q->limit($limit);
            }

            $allModels = $q->all();
        }

        if (!$allModels) {
            return;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
        ]);

        return $dataProvider;
    }
}
