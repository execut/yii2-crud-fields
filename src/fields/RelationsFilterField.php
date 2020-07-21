<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use unclead\multipleinput\MultipleInputColumn;

/**
 * Field for render is has relation filter
 * @package execut\crudFields
 */
class RelationsFilterField extends Field
{
    /**
     * {@inheritDoc}
     */
    public $attribute = 'isHasRelation';
    /**
     * {@inheritDoc}
     */
    public function getMultipleInputField()
    {
        return [
            'type' => MultipleInputColumn::TYPE_DROPDOWN,
            'name' => $this->attribute,
            'items' => [
                '' => '',
                '1' => 'Есть',
                '0' => 'Нет',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[$this->attribute . 'SafeDefault'] = [[$this->attribute], 'safe'];

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function getField()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn()
    {
        return false;
    }
}
