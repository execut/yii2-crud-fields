<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use unclead\multipleinput\MultipleInputColumn;
use yii\base\Exception as ExceptionAlias;

/**
 * Field for primary key hidden input
 * @package execut\crudFields\fields
 */
class Id extends Field
{
    /**
     * {@inheritdoc}
     */
    protected $attribute = 'id';

    /**
     * {@inheritdoc}
     */
    public $multipleInputType = MultipleInputColumn::TYPE_HIDDEN_INPUT;

    /**
     * {@inheritdoc}
     */
    public $displayOnly = true;

    /**
     * {@inheritdoc}
     * @throws ExceptionAlias
     */
    public function getField()
    {
        if (!$this->getValue()) {
            return false;
        }

        return parent::getField();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = $this->rules;
        if ($this->getAttribute() !== null) {
            $rules[] = [
                [$this->getAttribute()],
                'safe',
                'on' => self::SCENARIO_GRID,
            ];
        }

        $rules[$this->getAttribute() . '_filter'] = [
            $this->getAttribute(),
            'filter',
            'filter' => function ($v) {
                if (is_string($v)) {
                    $column = $this->model->getTableSchema()->getColumn($this->getAttribute());
                    if ($column) {
                        return $column->phpTypecast($v);
                    }
                }

                return $v;
            },
            'on' => self::SCENARIO_GRID
        ];

        return $rules;
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
    protected function initDetailViewField(DetailViewField $field)
    {
        $field->setDisplayOnly(true);
    }
}
