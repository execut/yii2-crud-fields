<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\grid\ActionColumn;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class Action extends Field
{
    public $update = null;
    public $view = false;
    public $delete = null;
    public $order = 1000;
    public $_column = [
        'options' => [
            'style' => [
                'min-width' => '156px',
            ],
        ],
        'updateOptions' => [
            'class' => 'btn btn-primary',
            'label' => 'Просмотр',
        ],
        'deleteOptions' => [
            'class' => 'btn btn-danger glyphicon glyphicon-remove',
            'label' => ''
        ],
    ];

    public function getField()
    {
        return false;
    }

    public function getColumn()
    {
        $column = [
            'class' => ActionColumn::class,
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

        $column['template'] = $template;

        return ArrayHelper::merge($this->_column, $column);
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