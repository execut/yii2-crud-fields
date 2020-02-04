# execut/yii2-crud-fields

Этот компонент позволяет задать все настройки для интерфейса CRUD модели максимально быстрым способом. Например, нам
нужен CRUD для управления пользователями.

У нас есть таблица стандартная таблица пользователей с полями:


Поведение поддерживает плагины. Пример:
```php
<?php
namespace execut\calls;

use execut\crudFields\Behavior;
use execut\crudFields\BehaviorStub;
use execut\crudFields\ModelsHelperTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Call extends ActiveRecord
{
    use BehaviorStub, ModelsHelperTrait;
    const MODEL_NAME = '{n,plural,=0{Calls} =1{Call} other{Calls}}';
    public function behaviors()
    {
        return [
            'fields' => [
                'class' => Behavior::class,
                'fields' => $this->getStandardFields(['name'], ['phone']),
                'plugins' => \yii::$app->getModule('calls')
                    ->getCallsCrudFieldsPlugins(),
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
```
