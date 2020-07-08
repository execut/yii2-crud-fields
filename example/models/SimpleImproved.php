<?php


namespace execut\crudFields\example\models;


use execut\crudFields\Behavior;
use execut\crudFields\BehaviorStub;
use yii\db\ActiveRecord;

class SimpleImproved extends ActiveRecord
{
    use BehaviorStub;
    public function behaviors() {
        return [
            Behavior::KEY => [
                'class' => Behavior::class,
                'fields' => [
                    'id' => [
                        'class' => \execut\crudFields\fields\Id::class,
                    ],
                    'name' => [
                        'class' => \execut\crudFields\fields\StringField::class,
                        'attribute' => 'name',
                        'required' => true,
                    ]
                ],
            ],
        ];
    }

    public static function tableName()
    {
        return 'example_simple';
    }
}