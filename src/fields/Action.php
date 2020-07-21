<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields;

use kartik\grid\ActionColumn;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Field for rendering actions column
 * @package execut\crudFields\fields
 */
class Action extends Field
{
    /**
     * @var callable|bool Renderer callback for update button.
     * null for default value
     * false for disable
     */
    public $update = false;
    /**
     * @var callable|bool Renderer callback for view button.
     * null for default value
     * false for disable
     */
    public $view = false;
    /**
     * @var callable|bool Renderer callback for delete button.
     * null for default value
     * false for disable
     */
    public $delete = null;
    /**
     * {@inheritdoc}
     */
    public $order = 1000;
    /**
     * @var bool Is use kartik\grid\ActionColumn column class
     */
    public $isKartik = false;

    /**
     * Field is not exists
     * @return false
     */
    public function getField()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        $parentColumn = parent::getColumn();
        if ($parentColumn === false) {
            return false;
        }

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

        if ($this->isKartik) {
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

            $column['template'] = $template;
        } else {
            $column = [
                'class' => \yii\grid\ActionColumn::class,
            ];
        }

        $column = ArrayHelper::merge($parentColumn, $column);
        unset($column['attribute']);

        return $column;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return ['actions' => $this->getColumn()];
    }

    /**
     * The field has no rules
     * @return false
     */
    public function rules()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function applyScopes(ActiveQueryInterface $query)
    {
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleInputField()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayOnly()
    {
        return true;
    }
}
