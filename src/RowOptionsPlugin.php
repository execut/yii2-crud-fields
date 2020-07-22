<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

/**
 * Interface of plugin for getting options for grid row
 * @package execut\crudFields
 */
interface RowOptionsPlugin
{
    /**
     * Returns options for grid row
     * @return array
     */
    public function getRowOptions();
}
