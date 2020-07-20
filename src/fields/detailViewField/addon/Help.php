<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon;

use execut\crudFields\fields\detailViewField\addon\help\Text;
use execut\crudFields\widgets\Qtip;
use yii\helpers\ArrayHelper;

/**
 * Help addon for DetailViewField
 * @package execut\crudFields\fields\detailViewField\addon
 */
class Help implements AddonInterface
{
    /**
     * @var Text Text instance for field addon
     */
    protected Text $text;
    /**
     * @var int|null Instances counter for generation unique id of qTip widget
     */
    protected ?int $instanceCount = null;
    /**
     * @var int Instances counter for setting instanceCount
     */
    protected static int $instancesCounter = 0;

    /**
     * Help constructor.
     * @param Text $text Text for help tooltip
     */
    public function __construct(Text $text)
    {
        $this->text = $text;
        self::$instancesCounter++;
        $this->instanceCount = self::$instancesCounter;
    }

    /**
     * Returns current text instance
     * @return Text
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Resets the instances counter for unit tests
     */
    public static function resetInstancesCounter() {
        self::$instancesCounter = 0;
    }

    /**
     * Get options value
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'id' => $this->getId(),
            'style' => 'cursor:pointer',
        ];
    }

    /**
     * Generate and return content for addon with qTip widget
     * @return string
     * @throws \Exception
     */
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

    /**
     * @inheritDoc
     */
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
     * Returns current qTip widget id
     * @return string
     */
    protected function getId(): string
    {
        return 'help-addon-' . $this->instanceCount;
    }
}