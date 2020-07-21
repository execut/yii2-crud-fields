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

/**
 * Field for password widget ClearedPasswordInput
 * @package execut\crudFields
 * @see ClearedPasswordInput
 */
class PasswordWidget extends Field
{
    /**
     * @var string User attribute for validate password
     */
    public $userAttribute = null;
    /**
     * @var bool|callable Is check password strength
     */
    public $isCheckStrength = true;
    /**
     * {@inheritDoc}
     */
    public function getField()
    {
        return [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $this->attribute,
            'widgetOptions' => [
                'class' => ClearedPasswordInput::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRules():array
    {
        if (!$this->isCheckStrength()) {
            return [];
        }

        return [
            'passwordStrength_' . $this->attribute   => [['password'], StrengthValidator::class, 'preset' => StrengthValidator::MEDIUM  , 'userAttribute'=> $this->userAttribute],
        ];
    }

    /**
     * Calculate and returns is check password strength
     * @return bool
     */
    protected function isCheckStrength()
    {
        $isCheckStrength = $this->isCheckStrength;
        if (is_callable($isCheckStrength)) {
            return $isCheckStrength();
        }

        return $isCheckStrength;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultipleInputField()
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
