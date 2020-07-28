# Dynamic fields
Sometimes it becomes necessary to change the content of form fields on the fly. For this, for the fields were made
reloaders.

Attention! For dynamic fields to work, you must use the extended DetailView (\execut\actions\widgets\DetailView) from
the [execut/yii2-actions](https://github.com/execut/yii2-actions) package.
In the future, it is planned to move this widget into a separate package and connect it to yii2-crud-fields.

## Field reloaders
We have a field for displaying the arrival time of the car with the cargo:
```php
$finishTimeField = new \execut\crudFields\fields\Date([
    'attribute' => 'finish_time',
    'isTime' => true,
]);
```
Let's say we need to constantly see the fresh arrival time without refreshing the page. To implement this, you need to create and connect to the field a reloader with the Periodically type:
```php
$periodicallyType = new \execut\crudFields\fields\reloader\type\Periodically();
$finishTimeReloader = new \execut\crudFields\fields\reloader\Reloader($periodicallyType);
$finishTimeField->setReloaders([$finishTimeReloader]);
```
After that, the field will periodically be updated once every 10 seconds.

Let's say we only need to update it when the car is on the way.
```php
// To do this, we create the status field itself
$statusField = new \execut\crudFields\fields\DropDown([
    'attribute' => 'status',
    'data' => [
        'going' => 'В пути',
        'arrived' => 'Прибыла',
    ],
]);
// We indicate to him that it is necessary to periodically update the status
$statusReloader = new \execut\crudFields\fields\reloader\Reloader($periodicallyType);
$statusField->setReloaders([$statusReloader]);

// We create an target so that the reloader is triggered when the machine status is "going"
$finishTimeTarget = new \execut\crudFields\fields\reloader\Target($statusField);
$finishTimeTarget->setValues(['going']);
// We specify that the arrival time is updated only when the vehicle status is "going"
$finishTimeReloader->setTargets([$finishTimeTarget]);
```
As a result, the status field and arrival time will be updated every 10 seconds. The arrival time will be update when the vehicle status only is "going".

## Interdependent fields
Sometimes you need to make certain fields depend on the value of others.
A good example is the fields of our car models and brands.
We want the list of models to change when we select a different mark:
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

// Create update type - dependent
$type = new \execut\crudFields\fields\reloader\type\Dependent();

// We set that the model field needs to be updated when the mark changes:
$target = new \execut\crudFields\fields\reloader\Target($markField);

$reloader = new \execut\crudFields\fields\reloader\Reloader($type, [$target]);
$modelField->setReloaders([$reloader]);
```
After such manipulations, our model field will filter its list after changing the car mark.