<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\bootstrap\backend;

use execut\crud\navigation\Configurator;
use execut\crudFields\models\AllFields;
use execut\navigation\Component;

/**
 * Class Bootstrapper
 * @package execut\books\bootstrap\backend
 */
class Bootstrapper implements \execut\crud\bootstrap\Bootstrapper
{
    /**
     * {@inheritDoc}
     */
    public function bootstrapForAdmin(Component $navigation)
    {
        $navigation->addConfigurator([
            'class' => Configurator::class,
            'module' => 'crudFields',
            'moduleName' => 'CRUD fields',
            'modelName' => AllFields::MODEL_NAME,
            'controller' => 'fields',
        ]);
    }
}
