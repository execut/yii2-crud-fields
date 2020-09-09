<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\relation;


use execut\crudFields\fields\Field;
use execut\crudFields\Relation;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class HasManyScope
{
    protected ActiveQueryInterface $query;
    protected Value $value;
    protected Relation $relation;
    protected ?ActiveRecord $model = null;
    protected ?bool $isApplyScope = null;
    public function __construct(ActiveQueryInterface $query, Value $value, Relation $relation)
    {
        $this->query = $query;
        $this->value = $value;
        $this->relation = $relation;
    }

    /**
     * @param bool $isApplyScope
     */
    public function setIsApplyScope(?bool $isApplyScope): void
    {
        $this->isApplyScope = $isApplyScope;
    }

    /**
     * @param ActiveRecord $model
     */
    public function setModel(?ActiveRecord $model): void
    {
        $this->model = $model;
    }

    public function applyScopes()
    {
        /**
         * @TODO Учесть with=false
         */
        $columnRecordsLimit = $this->relation->getColumnRecordsLimit();
        if ($columnRecordsLimit === null || $columnRecordsLimit === false) {
            $this->query->with($this->relation->getWith());
        }

        if ($this->isApplyScope === false) {
            return $this->query;
        }

        $errors = $this->model->getErrors();
        if (!empty($errors)) {
            return $this->query->andWhere('false');
        }

        $this->relation->applyScopeIsExistRecords($this->query);

        foreach ($this->value->getValue() as $rowModel) {
            $row = array_filter($rowModel->attributes);
            if (!empty($row)) {
                $relatedModelClass = $this->relation->getRelationModelClass();
                $relatedModel = new $relatedModelClass;

                $relatedModel->scenario = Field::SCENARIO_GRID;
                $relatedModel->attributes = $row;
                $relationQuery = clone $this->relation->getQuery();
                $relationQuery = $relatedModel->applyScopes($relationQuery);

                $relationQuery->select(key($relationQuery->link));
                $relationQuery->indexBy = key($relationQuery->link);
                if (!($this->model instanceof \execut\oData\ActiveRecord)) {
                    $attributePrefix = $this->model->tableName() . '.';
                } else {
                    $attributePrefix = '';
                }

                $relatedAttribute = current($relationQuery->link);
                $relationQuery->primaryModel = null;
                $relationQuery->link = null;

                $this->query->andWhere([
                    $attributePrefix . $relatedAttribute => $relationQuery,
                ]);
            }
        }

        return $this->query;
    }
}
