<?php
/**
 */

namespace execut\crudFields\example\models;


use execut\crudFields\Behavior;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Id;
use yii\base\Model;

class AllFieldsModel extends Model
{
    public function behaviors() {
        return [
            'fields' => [
                'class' => Behavior::class,
                'fields' => [
                    'id' => [
                        'class' => Id::class,
                    ],
                    'bool' => [
                        'class' => Boolean::class,
                        'attribute' => 'bool',
                    ]
                ]
            ],
        ];
    }
}