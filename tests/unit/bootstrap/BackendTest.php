<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\bootstrap\tests\unit;

use Codeception\Test\Unit;
use execut\crudFields\Module;
use execut\crudFields\bootstrap\Backend;
use yii\helpers\UnsetArrayValue;

/**
 * CommonTest
 * @package execut\books
 */
class BackendTest extends Unit
{
    public function testBootstrap()
    {
        $bootstrap = new Backend([
            'depends' => [
                'bootstrap' => new UnsetArrayValue(),
            ],
        ]);
        $bootstrap->bootstrap(\yii::$app);
        $this->assertInstanceOf(Module::class, \yii::$app->getModule('crudFields'));
    }
}
