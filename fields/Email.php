<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 12/15/17
 * Time: 9:45 AM
 */

namespace execut\crudFields\fields;


use yii\helpers\ArrayHelper;

class Email extends Field
{
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            $this->attribute . 'Email' => [
                $this->attribute, 'email'
            ],
        ]);
    }
}