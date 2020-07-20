<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use yii\helpers\Inflector;
class Translit extends Field
{
    public $transliteratedAttribute = null;
    public function getField()
    {
        $field = parent::getField(); // TODO: Change the autogenerated stub

        return $field;
    }

    public function rules()
    {
        $rules = parent::rules(); // TODO: Change the autogenerated stub
        $rules[$this->attribute . '_translit'] = [$this->attribute, 'default', 'skipOnEmpty' => false, 'except' => 'grid', 'value' => function () {
            return $this->getDefaultValue();
        }];

        return $rules;
    }

    protected function getDefaultValue() {
        if ($transliteratedAttribute = $this->transliteratedAttribute) {
            return Inflector::transliterate($this->model->$transliteratedAttribute);
        }
    }
}