# Система плагинов
Допустим, мы хотим сделать в простом примере c книгами поля, необходимые для вывода seo-метатегов книги на сайте.
Причём так, чтобы модель Book не знала об этом. Это необходимо для того, чтобы:
* разделить бизнес-логику модуля, отвечающего за книги и модуля, отвечающего за SEO
* исключить дублирование функционала работы с SEO-полями
* оставить модель Book такой-же простой как и раньше

С данной целью мы будем подключать к модели Book плагин [Fields](https://github.com/execut/yii2-seo/blob/master/crudFields/Fields.php)
из пакета [execut/yii2-seo](https://github.com/execut/yii2-seo). Чтобы было возможно его подключить к нашему примеру
через настройки приложения, необходимо расширить модуль execut\books\Module, добавив туда параметр booksPlugins для подключения
плагинов раздела книг:
```php
<?php
namespace execut\books;
class BooksModule extends \execut\booksNative\Module
{
    public $controllerNamespace = 'execut\booksNative\controllers';
    public $booksPlugins = [];
}
```
Затем расширяем модель книг Book до [BookPluggable](https://github.com/execut/yii2-books/blob/master/models/BookPluggable.php) для возможности подключать к ней плагины из конфигурации своего модуля:
```php
namespace execut\books\models;
use execut\crudFields\Behavior;
use yii\helpers\ArrayHelper;

class BookPluggable extends Book
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            Behavior::KEY => [
                'plugins' => \yii::$app->getModule('crudExample')->booksPlugins,
            ]
        ]);
    }
}
```

Можно было обойтись без отдельной модели, но для чистоты первого примера был выбран путь с наследованием.

Теперь мы подготовили модуль [execut/yii2-books](https://github.com/execut/yii2-books) для подключения сторонних плагинов. Далее, чтобы нам расширять модуль yii2-books
без лишней связанности, будем писать весь код, связывающий [execut/yii2-seo](https://github.com/execut/yii2-seo) и [execut/yii2-books](https://github.com/execut/yii2-books) в рамках отдельного
интеграционного модуля [execut/yii2-books-seo](https://github.com/execut/yii2-books-seo).

Создаём в нём миграцию для добавления SEO полей:
```php
<?php
namespace execut\booksSeo\migrations;

use execut\seo\migrations\AddFieldsHelper;
use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;
class m200714_140947_addSeoExampleFields extends Migration
{
    public function initInverter(Inverter $i)
    {
        $helper = new AddFieldsHelper([
            'table' => $i->table('example_books'),
        ]);
        $helper->attach();
    }
}
```

И применяем её
```shell script
./yii migrate/up --migrationPath=vendor/execut/yii2-books-seo/migrations --interactive=0
```

Затем собираем всё это дело в конфигурацию backend приложения:
```php
return [
    'bootstrap' => [
        'crudExample' => [
            'class' => \execut\booksNative\bootstrap\Common::class,
            'moduleConfig' => [
                'class' => \execut\books\BooksModule::class,
                'bookModelClass' => \execut\books\models\BookPluggable::class,
                'booksPlugins' => [
                    [
                        'class' => \execut\seo\crudFields\Fields::class,
                    ],
                ],
            ]
        ],
    ]
];
```
И у нас должна появится новая группа полей SEO:
![Форма](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/i/books-form-pluggable.jpg)

Плагин от [execut\seo\crudFields\Fields](https://github.com/execut/yii2-seo/blob/master/crudFields/Fields.php) наследует абстрактный класс для
плагинов [execut\crudFields\Plugin](https://github.com/execut/yii2-crud-fields/blob/master/Plugin.php) и переопределяет
в нём метод _getFields, который возвращает массив с конфигурацией новых полей.

Так-же в плагинах можно задать ряд свойств и переопределить другие методы: 
* **owner**: _Model_, через это свойство можно обратиться к экземпляру модели, которую плагин расширяет. Может понадобиться
в любом месте плагина
* **initDataProvider(DataProviderInterface $dataProvider)**: может потребоваться для инициализации поставщика данных модели,
например для увеличения размера страницы в списке CRUD
* **applyScopes(ActiveQuery $q)**: для инициализирования запроса, например, для добавления новых условий или выборок.
* **attach()**: для необходимости что либо произвести перед подключением плагина к модели
* и методы для перехвата одноимённых событий ActiveRecord
    * **afterUpdate()**
    * **afterInsert()**
    * **beforeValidate()**
    * **afterValidate()**
    * **beforeUpdate()**
    * **beforeInsert()**
    * **beforeSave()**
    * **afterSave()**
    * **beforeDelete()**
    * **afterLoad()**