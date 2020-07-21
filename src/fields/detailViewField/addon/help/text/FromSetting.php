<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help\text;

use execut\crudFields\fields\detailViewField\addon\help\Text;

/**
 * Text class to gets text from settings component of application
 * @see https://github.com/execut/yii2-settings
 * @package execut\crudFields
 */
class FromSetting implements Text
{
    /**
     * @var string Key of setting value
     */
    protected string $key;

    /**
     * FromSetting constructor.
     * @param string $key Key of setting value
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return \yii::$app->settings->get($this->key);
    }
}
