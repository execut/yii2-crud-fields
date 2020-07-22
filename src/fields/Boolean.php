<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\detail\DetailView;
use unclead\multipleinput\MultipleInputColumn;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Boolean CRUD field for rendering boolean column and form field
 * @package execut\crudFields\fields
 */
class Boolean extends Field
{
    /**
     * {@inheritdoc}
     */
    public $multipleInputType = MultipleInputColumn::TYPE_CHECKBOX;

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        $column = parent::getColumn();
        if ($column === false) {
            return false;
        }

        return ArrayHelper::merge([
            'filter' => [
                'Нет',
                'Да',
            ]
        ], $column);
    }

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        if ($this->getDisplayOnly()) {
            $value = function () {
                return $this->getValueString();
            };
        } else {
            $value = null;
        }

        $result = [
            'type' => DetailView::INPUT_CHECKBOX,
        ];

        if ($value !== null) {
            $result['value'] = $value;
        }

        return array_merge($field, $result);
    }

    /**
     * Returned flag value as string label
     * @return string
     * @throws Exception
     */
    protected function getValueString()
    {
        if ($this->getValue()) {
            return 'Да';
        } elseif (!$this->getValue() && $this->getValue() !== '') {
            return 'Нет';
        }

        return null;
    }
}
