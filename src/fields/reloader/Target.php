<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\reloader;

use execut\crudFields\fields\Field;

/**
 * Class reloader target
 * @package execut\crudFields\fields\reloader
 */
class Target
{
    /**
     * @var Field|null Target CRUD field
     */
    protected ?Field $field = null;
    /**
     * @var array|null The values of field for activation reloader
     */
    protected ?array $values = null;
    /**
     * @var bool|null Is activate reloader when field value is empty or not empty
     */
    protected ?bool $whenIsEmpty = null;

    /**
     * Target constructor
     * @param Field $field Target CRUD field
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Returns CRUD field
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set CRUD field
     * @param Field $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }

    /**
     * Get the values of field for activation reloader
     * @return array|null
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
     * @param array $values Set the values of field for activation reloader
     */
    public function setValues($values): void
    {
        $this->values = $values;
    }

    /**
     * Returns is activate reloader when field value is empty or not empty
     * @return boolean|null
     */
    public function getWhenIsEmpty()
    {
        return $this->whenIsEmpty;
    }

    /**
     * Set is activate reloader when field value is empty or not empty
     * @param null $whenIsEmpty Is activate reloader when field value is empty or not empty
     */
    public function setWhenIsEmpty($whenIsEmpty): void
    {
        $this->whenIsEmpty = $whenIsEmpty;
    }
}
