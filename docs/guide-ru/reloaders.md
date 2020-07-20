# Динамичные поля
Иногда возникает необходимость менять содержимое полей формы "на лету". Для этого для полей были сделаны
перезагрузчики.

Внимание! Для работы динамических полей необходимо использовать расширенный DetailView (\execut\actions\widgets\DetailView) из пакета [execut/yii2-actions](https://github.com/execut/yii2-actions).
В будущем планируется вынести этот виджет в отдельный пакет и подключить его к yii2-crud-fields.

## Перезагрузчики полей
У нас есть поле для отображения времени прибытия машины с грузом:
```php
$finishTimeField = new \execut\crudFields\fields\Date([
    'attribute' => 'finish_time',
    'isTime' => true,
]);
```
Допустим, нам необходимо постоянно видеть свежее время её прибытия без обновления страницы. Чтобы это реализовать, необходимо создать и
подключить к полю перезагрузчик с типом Periodically:
```php
$periodicallyType = new \execut\crudFields\fields\reloader\type\Periodically();
$finishTimeReloader = new \execut\crudFields\fields\reloader\Reloader($periodicallyType);
$finishTimeField->setReloaders([$finishTimeReloader]);
```
После этого поле станет периодически обновляться раз в 10 секунд.

Допустим нам нужно его обновлять только тогда, когда машина находится в пути.
```php
// Для этого создаём само поле статуса
$statusField = new \execut\crudFields\fields\DropDown([
    'attribute' => 'status',
    'data' => [
        'going' => 'В пути',
        'arrived' => 'Прибыла',
    ],
]);
// Указываем ему, что необходимо периодически обновлять статус
$statusReloader = new \execut\crudFields\fields\reloader\Reloader($periodicallyType);
$statusField->setReloaders([$statusReloader]);

// Создаём цель для того, чтобы перезагрузчик срабатывал когда статус машины является going
$finishTimeTarget = new \execut\crudFields\fields\reloader\Target($statusField);
$finishTimeTarget->setValues(['going']);
// Указываем, чтобы время прибытие обновлялось только тогда, когда статус машины "В пути"
$finishTimeReloader->setTargets([$finishTimeTarget]);
```
В результате поле статуса и время прибытия будут обновляться раз в 10 секунд, при этом время прибытия будет делать это
только когда статус машины "В пути"

## Взаимозависимые поля
Иногда нужно сделать так, чтобы одни поля зависели от значения других. Хороший пример - это поля моделей и марок наших
машин. Нам необходимо, чтобы список моделей менялся когда мы выбираем другую марку:
```php
$markField = new \execut\crudFields\fields\DropDown([
    'attribute' => 'cars_mark_id',
    'relationName' => 'mark',
    'relationQuery' => $this->hasOne(Mark::class, ['id' => 'cars_mark_id']),
]);
$modelField = new \execut\crudFields\fields\DropDown([
    'attribute' => 'cars_model_id',
    'relationName' => 'model',
    'relationQuery' => $this->hasOne(Model::class, ['id' => 'cars_model_id']),
    'data' => $this->getModelsListByMark(),
]);

// Создаём тип обновления - зависимый
$type = new \execut\crudFields\fields\reloader\type\Dependent();

// Задаём, что поле модели необходимо обновлять когда меняется марка:
$target = new \execut\crudFields\fields\reloader\Target($markField);

$reloader = new \execut\crudFields\fields\reloader\Reloader($type, [$target]);
$modelField->setReloaders([$reloader]);
```
После таких манипуляций наше поле модели будет фильтровать свой список после изменения марки.