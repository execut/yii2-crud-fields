<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\reloader\type;
use execut\crudFields\fields\reloader\TypeInterface;
class Periodically implements TypeInterface
{
    public function getKey(): string
    {
        return 'periodically';
    }
}