<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit;

use Codeception\Test\Unit;
use execut\crudFields\Behavior;
use execut\crudFields\BehaviorStub;
use execut\crudFields\fields\HasOneSelect2;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BehaviorStubTest extends Unit
{
    public function testBug()
    {
        $model = new ToDbCharacteristicTestModel;
        $model->dbCharacteristic;
    }
}

class ToDbCharacteristicTestModel extends ActiveRecord
{
    use BehaviorStub;
    public function behaviors()
    {
        return [
            Behavior::KEY => [
                'class' => Behavior::class,
                'fields' => [
                    'has' => [
                        'class' => HasOneSelect2::class,
                        'attribute' => 'test',
                        'relation' => 'test',
                        'label' => 'test',
                    ]
                ],
            ]
        ];
    }

    public function getTest()
    {
        return $this->hasOne(self::class, []);
    }

    public function getDbCharacteristic()
    {
        return 1;
    }

    public function hasAttribute($name)
    {
        return true; // TODO: Change the autogenerated stub
    }
}
