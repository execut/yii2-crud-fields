<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields\HasManyMultipleInput;

use Codeception\Test\Unit;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\HasManyMultipleInput;
use execut\crudFields\fields\HasManyMultipleInput\GridRenderer;
use execut\crudFields\models\AllFields;
use execut\crudFields\widgets\TestGridViewWidget;
use yii\base\Exception;

class GridRendererTest extends Unit
{
    public function testRenderGridWithCallbackDefaultOptions()
    {
        $gridRenderer = new GridRenderer(function () {
            return [
                'class' => TestGridViewWidget::class,
            ];
        });

        $options = $this->getMockBuilder(GridRenderer\Params::class)->getMock();
        $options->expects($this->once())
            ->method('toArray')
            ->willReturn([
            ]);

        $gridRenderer->setParams($options);

        TestGridViewWidget::$isRunned = false;
        $gridRenderer->render();
        $this->assertTrue(TestGridViewWidget::$isRunned);
    }


    public function testRenderGridWithArrayDefaultOptions()
    {
        $gridRenderer = new GridRenderer([
            'test' => 'test',
        ]);

        $options = $this->getMockBuilder(GridRenderer\Params::class)->getMock();
        $options->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'class' => TestGridViewWidget::class,
            ]);

        $gridRenderer->setParams($options);

        TestGridViewWidget::$test = null;
        TestGridViewWidget::$isRunned = false;
        $gridRenderer->render();
        $this->assertTrue(TestGridViewWidget::$isRunned);
        $this->assertEquals('test', TestGridViewWidget::$test);
    }

    public function testRenderWithEmptyParams()
    {
        $gridRenderer = new GridRenderer([]);
        $params = $this->getMockBuilder(GridRenderer\Params::class)->getMock();
        $params->method('toArray')->willReturn(null);
        $gridRenderer->setParams($params);
        $this->assertEquals(null, $gridRenderer->render());
    }
}
