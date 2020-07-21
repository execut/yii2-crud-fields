<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use yii\db\ActiveQueryInterface;
use yii\db\mysql\Schema;

/**
 * Field for sting input
 * @package execut\crudFields\fields
 */
class StringField extends Field
{
    /**
     * @var integer Min text length
     */
    public $minLength = null;
    /**
     * @var integer Max text length
     */
    public $maxLength = 255;
    /**
     * {@inheritDoc}
     */
    public function applyScopes(ActiveQueryInterface $query)
    {
        $value = $this->getValue();
        if ($value) {
            $attribute = $this->attribute;
            if (($db = $this->model->getDb()) && ($schema = $db->getSchema()) && $schema instanceof Schema) {
                $operator = 'LIKE';
            } else {
                $operator = 'ILIKE';
            }

            $query->andWhere([
                $operator,
                $attribute,
                $value,
            ]);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRules():array
    {
        $rules = parent::getRules();
        if ($this->maxLength || $this->minLength) {
            $rule = [[$this->attribute], 'string'];
            if ($this->maxLength) {
                $rule['max'] = $this->maxLength;
            }

            if ($this->minLength) {
                $rule['min'] = $this->minLength;
            }

            $rules['string_' . $this->attribute] = $rule;
        }

        return $rules;
    }
}
