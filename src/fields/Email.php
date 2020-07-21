<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use yii\helpers\ArrayHelper;

/**
 * Field for emails
 * @package execut\crudFields\fields
 */
class Email extends Field
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            $this->attribute . 'Email' => [
                $this->attribute, 'email'
            ],
        ]);
    }
}