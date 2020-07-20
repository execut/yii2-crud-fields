<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help\text;

use execut\crudFields\fields\detailViewField\addon\help\Text;

/**
 * Class for simple text value
 * @package execut\crudFields
 */
class Simple implements Text
{
    /**
     * @var string Value
     */
    protected string $value;

    /**
     * Simple constructor.
     * @param string $value Text string value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }
}
