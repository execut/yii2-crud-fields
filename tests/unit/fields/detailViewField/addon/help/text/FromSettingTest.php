<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;
use execut\settings\Component;

class FromSettingTest extends \Codeception\Test\Unit
{
    public function testGetValue() {
        $setting = $this->getMockBuilder(Component::class)->getMock();
        $settingsKey = 'settings key';
        $value = 'test value';
        $setting->expects($this->once())
            ->method('get')
            ->with($settingsKey)
            ->willReturn($value);
        \yii::$app->set('settings', $setting);
        $text = new FromSetting($settingsKey);
        $this->assertEquals($value, $text->getValue());
        \yii::$app->set('settings', null);
    }
}