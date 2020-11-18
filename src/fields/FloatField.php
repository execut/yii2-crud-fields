<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use yii\helpers\ArrayHelper;

/**
 * Field for float numbers
 * @package execut\crudFields
 */
class FloatField extends Field
{
    /**
     * @var int Minimal possible value
     */
    public $min = 0;
    /**
     * @var int Maximum possible value
     */
    public $max = 2000000000;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::getPreparedRules(), [
            $this->attribute . 'float' => [[$this->attribute], 'double', 'min' => $this->min, 'max' => $this->max]
        ]);
    }
}
