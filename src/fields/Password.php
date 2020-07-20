<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use yii\helpers\ArrayHelper;
class Password extends Field
{
    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        $options = $this->getOptions();

        if (!$this->getValue()) {
            $options['onload'] = '$(this).val(\'\')';
        }

        return ArrayHelper::merge($field, [
            'options' => $options,
        ]);
    }

    public function getMultipleInputField()
    {
        $result = parent::getMultipleInputField(); // TODO: Change the autogenerated stub
        $result['options'] = $this->getOptions();

        return $result;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'type' => 'password',
            'autocomplete' => 'off',
        ];
    }

    public function getColumn()
    {
        return false;
    }
}