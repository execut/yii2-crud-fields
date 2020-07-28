<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\tests\unit\widgets;

use execut\crudFields\TestCase;
use execut\crudFields\widgets\Select2;
use Yii;
use yii\di\Container;
use yii\helpers\ArrayHelper;

class Select2Test extends \Codeception\Test\Unit
{
    protected function setUp():void
    {
        parent::setUp();
//        $this->mockWebApplication([
//            'basePath' => __DIR__ . '/../../../../../../',
//            'vendorPath' => __DIR__ . '/../../../../../',
//            'components' => [
//                'assetManager' => [
//                    'basePath' => __DIR__ . '/../../assets',
//                    'baseUrl' => '/assets',
//                ],
//            ]
//        ]);
    }

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown():void
    {
        parent::tearDown();
//        $this->destroyApplication();
    }

    public function testSelectWrapperAndRegisterWidget()
    {
        $result = Select2::widget([
            'id' => 'test-widget',
            'name' => 'test',
        ]);

        $out = Yii::$app->view->renderFile(__DIR__ . '/../../views/layout.php', [
            'content' => $result,
        ]);

        $this->assertEqualsWithoutLE('<div class="select2-execut" id="test-widget-execut"><div class="kv-plugin-loading loading-test-widget">&nbsp;</div><select id="test-widget" class="form-control" name="test" data-s2-options="s2options_d6851687" data-krajee-select2="select2_50f2809b" style="width: 1px; height: 1px; visibility: hidden;">
<option value=""></option>
</select><div class="select-label"></div></div>', $result);

        $this->assertStringContainsString(
            '$("#test-widget-execut").Select2Execut();',
            $out,
            'There should be query widget ExecutSelect2');
//        $this->assertRegExp(
//            '~<script src="/assets/[0-9a-f]+/Select2Execut.js"></script>~',
//            $out,
//            'There should be Select2Execut asset registered.'
//        );
    }

    /**
     * Asserting two strings equality ignoring line endings
     *
     * @param string $expected
     * @param string $actual
     */
    public function assertEqualsWithoutLE($expected, $actual)
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual);
    }

    public function testWidgetWithArrayValue()
    {
        Select2::widget([
            'id' => 'test-widget',
            'name' => 'test',
            'value' => [],
            'initValueText' => [
                'test'
            ]
        ]);
    }
}
