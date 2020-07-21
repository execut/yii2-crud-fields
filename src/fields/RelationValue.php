<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Field for rendering relation attribute value inside column
 * @package execut\crudFields
 */
class RelationValue extends Field
{
    /**
     * {@inheritDoc}
     */
    public $scope = false;
    /**
     * {@inheritDoc}
     */
    protected $_field = false;
    /**
     * {@inheritDoc}
     */
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

    /**
     * Returns relation attribute value from model instance
     * @param Model $row Model instance
     * @param int $currentKey Current key for recursion algorithm
     * @return array|mixed|null
     */
    protected function getAttributeValue($row, $currentKey = 0)
    {
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

        return null;
    }
}
