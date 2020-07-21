<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\reloader;

/**
 * Reloader type interface
 * @package execut\crudFields
 */
interface TypeInterface
{
    /**
     * Returns key of type for javascript
     * @return string
     */
    public function getKey():string;
}
