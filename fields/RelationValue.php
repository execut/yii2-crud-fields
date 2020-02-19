<?php


namespace execut\crudFields\fields;


use yii\helpers\ArrayHelper;

class RelationValue extends Field
{
    public $scope = false;
    protected $_field = false;
    public function getColumn()
    {
        return [
            'label' => $this->getLabel(),
            'value' => function ($row) {
                $value = $this->getAttributeValue($row);
                if (is_array($value)) {
                    $value = array_filter($value);

                    return implode(', ', $value);
                }

                return $value;
            },
        ];
    }

    protected function getAttributeValue($row, $currentKey = 0) {
        $attributeParts = explode('.', $this->attribute);
        $attribute = $attributeParts[$currentKey];
        if ($currentKey === count($attributeParts) - 1) {
            if (!is_array($row)) {
                return ArrayHelper::getValue($row, $attribute);
            } else {
                $result = [];
                foreach ($row as $value) {
                    $result[] = ArrayHelper::getValue($value, $attribute);
                }

                return $result;
            }
        }

        if (is_array($row)) {
            if (isset($row[$attribute])) {
                return $this->getAttributeValue($row[$attribute], $currentKey + 1);
            }

            $result = [];
            foreach ($row as $value) {
                $result[] = $this->getAttributeValue($value, $currentKey);
            }

            return $result;
        } else {
            if (is_object($row) && !empty($row->$attribute)) {
                return $this->getAttributeValue($row->$attribute, $currentKey + 1);
            }
        }
    }
}