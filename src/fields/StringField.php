<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use yii\db\ActiveQuery;
use yii\db\mysql\Schema;
class StringField extends Field
{
    public $minLength = null;
    public $maxLength = 255;
//    public $isPartially = true;
    public function applyScopes(ActiveQuery $query) {
        $value = $this->getValue();
        if ($value) {
//            if ($this->isPartially) {
//                $value = '%' . $value . '%';
//            }

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