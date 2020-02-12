<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\grid\ActionColumn;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class Action extends Field
{
    public $update = false;
    public $view = false;
    public $delete = null;
    public $order = 1000;
    protected $_column = [];

    public function getField()
    {
        return false;
    }

    public function getColumn()
    {
        $parentColumn = parent::getColumn();
        if ($parentColumn === false) {
            return false;
        }

        $column = [
            'class' => ActionColumn::class,
            'options' => [
                'style' => [
                    'min-width' => '156px',
                ],
            ],
//            'updateOptions' => [
//                'class' => 'btn btn-primary update',
//                'label' => 'Просмотр',
//            ],
            'deleteOptions' => [
                'class' => 'btn btn-danger glyphicon glyphicon-remove',
                'label' => ''
            ],
        ];

        $template = '';
        if ($this->update !== false) {
            $template .= '{update} ';
        }

        if ($this->view !== false) {
            $template .= '{view} ';
        }

        if ($this->delete !== false) {
            $template .= '{delete} ';
        }

        $template = trim($template);
        if ($template === '') {
            return false;
        }

        $column['template'] = $template;

        $column = ArrayHelper::merge($parentColumn, $column);
        unset($column['attribute']);

        return $column;
    }

    public function getColumns()
    {
        return ['actions' => $this->getColumn()];
    }

    public function rules()
    {
        return false;
    }

    public function applyScopes(ActiveQuery $query)
    {
        return $query;
    }

    public function getMultipleInputField()
    {
        return false;
    }
}