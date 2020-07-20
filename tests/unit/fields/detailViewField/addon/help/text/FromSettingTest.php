<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
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