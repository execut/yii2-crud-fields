<?php
/**
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help;

/**
 * Interface of help text
 * @package execut\crudFields
 */
interface Text
{
    /**
     * Returned value of addon text
     * @return string
     */
    public function getValue();
}