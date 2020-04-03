<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon;


use execut\crudFields\fields\detailViewField\Addon;
use execut\crudFields\fields\detailViewField\addon\help\Text;
use execut\crudFields\widgets\Qtip;
use yii\helpers\ArrayHelper;

class Help implements AddonInterface
{
    protected $text = null;
    protected $instanceCount = null;
    protected static $instancesCounter = 0;

    public function __construct(Text $text)
    {
        $this->text = $text;
        self::$instancesCounter++;
        $this->instanceCount = self::$instancesCounter;
    }

    public function getText() {
        return $this->text;
    }

    public static function resetInstancesCounter() {
        self::$instancesCounter = 0;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'id' => $this->getId(),
            'style' => 'cursor:pointer',
        ];
    }

    protected function getContent()
    {
        return '<i class="glyphicon text-primary glyphicon-question-sign"></i>' . Qtip::widget(ArrayHelper::merge([], [
                'content' => [
                    'text' => $this->text->getValue(),
                ],
                'position' => [
                    'my' => 'top right',
                    'at' => 'bottom left',
                    'viewport' => new \yii\web\JsExpression('$(document.body)'),
                    'adjust' => [
                        'method' => 'shift shift',
                        'scroll' => true,
                    ],
                ],
                'show' => [
                    'event' => 'click',
                    'effect' => new \yii\web\JsExpression('function () {$(this).slideDown();}'),
                ],
                'hide' => [
                    'event' => 'unfocus click',
                    'effect' => new \yii\web\JsExpression('function () {$(this).slideUp();}'),
                ],
                'style' => [
                    'classes' => 'qtip-bootstrap',
                    'width' => 'auto',
                ],
//                                        'options' => [
//                                            'id' => 'qtip-phone-icon',
//                                        ],
                'hook' => '#' . $this->getId(),
            ]));
    }

    public function getConfig()
    {
        return [
            'append' => [
                'options' => $this->getOptions(),
                'content' => $this->getContent(),
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getId(): string
    {
        return 'help-addon-' . $this->instanceCount;
    }
}