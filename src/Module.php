<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

/**
 * Books module
 * @package execut\books
 */
class Module extends \yii\base\Module implements \execut\crud\bootstrap\Module
{
    public $controllerNamespace = 'execut\crudFields\backend';
    /**
     * @var string Administrator role string
     */
    public $adminRole = '@';

    /**
     * Returns admin role of book module
     * @return string
     */
    public function getAdminRole()
    {
        return $this->adminRole;
    }
}
