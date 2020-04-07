<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon;

use execut\crudFields\fields\detailViewField\addon\help\Text;

class HelpTest extends \Codeception\Test\Unit
{
    public function testGetText() {
        $text = $this->getTextMock();
        $help = new Help($text);
        $this->assertEquals($text, $help->getText());
    }

    public function testGetGetConfig() {
        $text = $this->getTextMock();
        Help::resetInstancesCounter();
        $help = new Help($text);
        $config = $help->getConfig();
        $this->assertArrayHasKey('append', $config);
        $append = $config['append'];
        $this->assertArrayHasKey('options', $append);
        $options = $append['options'];
        $this->assertArrayHasKey('style', $options);
        $this->assertEquals('cursor:pointer', $options['style']);

        $this->assertArrayHasKey('content', $append);
        $content = $append['content'];
        $this->assertStringContainsString('<i class="glyphicon text-primary glyphicon-question-sign"></i>', $content);

        $js = \yii::$app->view->js;
        $this->assertCount(1, $js);
        $js = current($js);
        $js = current($js);
        $this->assertStringContainsString('"content":{"text":"test"}', $js);
        $this->assertStringContainsString('help-addon-1', $js);
    }


    public function testGetGetConfigId()
    {
        Help::resetInstancesCounter();
        $text = $this->getTextMock();
        $helpFirst = new Help($text);
        $this->assertEquals('help-addon-1', $helpFirst->getConfig()['append']['options']['id']);
        $this->assertEquals('help-addon-1', $helpFirst->getConfig()['append']['options']['id']);

        $helpSecond = new Help($text);
        $this->assertEquals('help-addon-2', $helpSecond->getConfig()['append']['options']['id']);
        $this->assertEquals('help-addon-1', $helpFirst->getConfig()['append']['options']['id']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTextMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        $text = $this->getMockBuilder(Text::class)->getMock();
        $text->method('getValue')->willReturn('test');
        return $text;
    }
}