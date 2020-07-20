<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use execut\crudFields\widgets\ClearedPasswordInput;
use kartik\detail\DetailView;
use kartik\password\StrengthValidator;
class PasswordWidget extends Field
{
    public $userAttribute = null;
    public $isCheckStrength = true;
    public function getField()
    {
        return [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $this->attribute,
            'widgetOptions' => [
                'class' => ClearedPasswordInput::class,
//                'options' => [
//                    'onready' => new JsExpression(<<<JAVASCRIPT
//alert(1);
//JAVASCRIPT
//)
//                ]
            ],
        ];
    }

    public function getRules():array
    {
        if (!$this->isCheckStrength()) {
            return [];
        }

        return [
            'passwordStrength_' . $this->attribute   => [['password'], StrengthValidator::class, 'preset' => StrengthValidator::MEDIUM  , 'userAttribute'=> $this->userAttribute],
        ]; // TODO: Change the autogenerated stub
    }

    public function isCheckStrength() {
        $isCheckStrength = $this->isCheckStrength;
        if (is_callable($isCheckStrength)) {
            return $isCheckStrength();
        }

        return $isCheckStrength;
    }

    public function getMultipleInputField()
    {
        return false;
    }

    public function getColumn()
    {
        return false;
    }
}