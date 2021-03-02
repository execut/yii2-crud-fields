<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use execut\crudFields\widgets\HasRelationDropdown;
use kartik\detail\DetailView;
use execut\crudFields\widgets\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Field for Select2 widget for has many relations
 * @package execut\crudFields
 */
class HasManySelect2 extends HasOneSelect2
{
    /**
     * @var array Via columns
     */
    public $viaColumns = [];

    /**
     * {@inheritdoc}
     */
    protected function applyFieldScopes(ActiveQueryInterface $query)
    {
        $this->applyRelationScopes($query);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return $field;
        }

        $sourceInitText = $this->getRelationObject()->getSourcesText();
        $relation = $this->getRelationObject();
        if ($relation->isVia()) {
            $viaRelationModelClass = $this->getRelationObject()->getViaRelationQuery()->modelClass;
            $viaRelationModel = new $viaRelationModelClass;
            $attribute = $this->getRelationObject()->getViaRelation();

            $fromAttribute = $this->getRelationObject()->getViaFromAttribute();
            $toAttribute = $this->getRelationObject()->getViaToAttribute();
            $relationName = $this->getRelationObject()->getName();
            if (empty($this->model->$attribute)) {
                $viaModelsAttributes = [];
                foreach ($this->model->$relationName as $model) {
                    $viaModelsAttributes[] = [
                        $fromAttribute => $model->primaryKey
                    ];
                }

                $this->model->$attribute = $viaModelsAttributes;
            }
        } else {
            $relationQuery = $this->getRelationObject()->getQuery();
            $viaRelationModelClass = $relationQuery->modelClass;
            $viaRelationModel = new $viaRelationModelClass;
            $attribute = $this->getRelationObject()->getName();

            $fromAttribute = key($relationQuery->link);
            $toAttribute = current($relationQuery->link);
        }

        $columns = ArrayHelper::merge([
            'from' => [
                'name' => $fromAttribute,
                'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                'defaultValue' => $this->model->primaryKey,
            ],
            'to' => [
                'title' => \yii::t('execut/' . $this->module, $this->model->getAttributeLabel($toAttribute)),
                'name' => $toAttribute,
                'type' => Select2::class,
                'defaultValue' => null,
                'value' => $sourceInitText,
                'options' => $this->getSelect2WidgetOptions(),
            ],
        ], $this->viaColumns);

        if ($toAttribute !== 'id' && empty($columns['id'])) {
            $columns['id'] = [
                'name' => 'id',
                'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
            ];
        }

        foreach ($columns as &$column) {
            if (!isset($column['title']) && !empty($column['name'])) {
                $column['title'] = Html::activeLabel($viaRelationModel, $column['name']);
            }
        }

        return ArrayHelper::merge([], [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $attribute,
            'label' => $this->getLabel(),
            'format' => 'raw',
            'value' => function () {
            },
            'widgetOptions' => [
                'class' => MultipleInput::class,
                'allowEmptyList' => true,
                'enableGuessTitle' => true,
                'model' => $viaRelationModel,
                'addButtonPosition' => MultipleInput::POS_HEADER,
                'columns' => $columns
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleInputField($relationModels = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreparedRules(): array
    {
        $rules = parent::getPreparedRules();
        if ($this->getRelationObject()->isVia()) {
            $viaRelationName = $this->getRelationObject()->getViaRelation();
            $rules[$viaRelationName] = [
                $viaRelationName,
                'safe',
            ];
        }

        unset($rules[$this->attribute . '_limit']);

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHasRelationFilter()
    {
        if (($relation = $this->getRelationObject()) && $this->isHasRelationAttribute) {
            return HasRelationDropdown::widget([
                'model' => $this->model,
                'attribute' => $this->isHasRelationAttribute,
                'parentId' => Html::getInputId($this->model, $this->attribute),
            ]);
        }

        return null;
    }
}
