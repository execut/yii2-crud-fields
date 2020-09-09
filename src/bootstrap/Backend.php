<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\bootstrap;

use execut\crud\bootstrap\Bootstrapper;
use execut\crudFields\Module;

class Backend extends \execut\crud\bootstrap\Backend
{
    public $isBootstrapI18n = true;
    /**
     * {@inheritDoc}
     */
    public $moduleId = 'crudFields';
    /**
     * {@inheritDoc}
     */
    protected $_defaultDepends = [
        'modules' => [
            'crudFields' => [
                'class' => Module::class,
            ]
        ]
    ];
    /**
     * {@inheritDoc}
     */
    public function getBootstrapper(): Bootstrapper
    {
        if ($this->bootstrapper === null) {
            $this->bootstrapper = new \execut\crudFields\bootstrap\backend\Bootstrapper;
        }

        return $this->bootstrapper;
    }
}
