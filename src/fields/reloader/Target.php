<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\reloader;
use execut\crudFields\fields\Field;
class Target
{
    protected ?Field $field = null;
    protected ?array $values = null;
    protected ?bool $whenIsEmpty = null;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param null $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }

    /**
     * @return null
     */
    public function getValues()
    {
        $result = $this->values;
        if ($result) {
            foreach ($result as $key => $value) {
                if (is_callable($value)) {
                    $result[$key] = $value();
                }
            }
        }

        return $result;
    }

    /**
     * @param null $values
     */
    public function setValues($values): void
    {
        $this->values = $values;
    }

    /**
     * @return boolean|null
     */
    public function getWhenIsEmpty()
    {
        return $this->whenIsEmpty;
    }

    /**
     * @param null $whenIsEmpty
     */
    public function setWhenIsEmpty($whenIsEmpty): void
    {
        $this->whenIsEmpty = $whenIsEmpty;
    }

}