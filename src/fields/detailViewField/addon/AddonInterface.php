<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon;

/**
 * Interface of addon for DetailViewField
 * @package execut\crudFields
 */
interface AddonInterface
{
    /**
     * Get result addon config
     * @return array[]
     */
    public function getConfig();
}
