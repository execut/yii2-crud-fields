<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use unclead\multipleinput\MultipleInputColumn;
class Id extends Field
{
    protected $attribute = 'id';
    public $multipleInputType = MultipleInputColumn::TYPE_HIDDEN_INPUT;
    public $displayOnly = true;

    public function getField()
    {
        if (!$this->getValue()) {
            return false;
        }

        return parent::getField();
    }

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

    public function getMultipleInputField()
    {
        return false;
    }

    protected function initDetailViewField(DetailViewField $field)
    {
        $field->setDisplayOnly(true);
    }
}