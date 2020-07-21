<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields;

/**
 * Interface ReloaderInterface
 * @package execut\crudFields\fields
 */
interface ReloaderInterface
{
    /**
     * Returns reloader type key
     * @return string
     */
    public function getKey():string;
}
