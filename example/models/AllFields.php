<?php
/**
 */

namespace execut\crudFields\example\models;


use execut\crudFields\Behavior;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\fields\Id;
use yii\base\Model;
use yii\db\ActiveRecord;

class AllFields extends ActiveRecord
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
                    ],
                    'hasOne' => [
                        'class' => HasOneSelect2::class,
                        'attribute' => 'has_one_id',
                        'relation' => 'hasOne',
                        'relationQuery' => $this->hasOne(self::class, [
                            'id' => 'has_one_id',
                        ]),
                    ]
                ]
            ],
        ];
    }
}