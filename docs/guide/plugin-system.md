# Plugin system
Let's say we want to make in a simple example with books the fields needed to display the seo meta tags of a book on the site.
And so that the Book model does not know about it.
This is necessary in order to:
* separate the business logic of the module responsible for books and the module responsible for SEO
* exclude duplication of functionality for working with SEO-fields
* keep the Book model as simple as before

For this purpose, we will connect the [Fields](https://github.com/execut/yii2-seo/blob/master/crudFields/Fields.php) plugin from the [execut/yii2-seo](https://github.com/execut/yii2-seo) package to the Book model.
To be able to connect it to our example through the application settings, it is necessary to extend the execut\books\Module module by adding the booksPlugins parameter to connect the plugins of the books section:
```php
<?php
namespace execut\books;
class BooksModule extends \execut\booksNative\Module
{
    public $controllerNamespace = 'execut\booksNative\controllers';
    public $booksPlugins = [];
}
```
Then we expand the book model Book to [BookPluggable](https://github.com/execut/yii2-books/blob/master/models/BookPluggable.php) to be able to connect plugins to it from the configuration of our module:
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

It was possible to do without a separate model, but for the purity of the first example, the path with inheritance was chosen.

Now we have prepared the [execut/yii2-books](https://github.com/execut/yii2-books) module for connecting third-party plugins.
Next, to extend the yii2-books module, we will write all the code linking [execut/yii2-seo](https://github.com/execut/yii2-seo) and [execut/yii2-books](https://github.com/execut/yii2-books) in a separate [execut/yii2-books-seo](https://github.com/execut/yii2-books-seo) integration module.
This will extendend the module yii2-seo without editing it.

Let's create a migration in it to add SEO fields:
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
and apply it
```shell script
./yii migrate/up --migrationPath=vendor/execut/yii2-books-seo/migrations --interactive=0
```

Then we collect the whole thing into the configuration of the backend application:
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
And we should have a new group of SEO fields:
![Форма](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/books-form-pluggable.jpg)
The plugin from [execut\seo\crudFields\Fields](https://github.com/execut/yii2-seo/blob/master/crudFields/Fields.php) inherits the abstract class for plugins [execut\crudFields\Plugin](https://github.com/execut/yii2-crud-fields/blob/master/Plugin.php) and overrides the _getFields method in it, which returns an array with the configuration of new fields.

Also, in plugins, you can set a number of properties and override other methods:
* **owner**: _Model_, through this property, you can refer to an instance of the model that the plugin extends.
May be needed anywhere in the plugin.
* **initDataProvider(DataProviderInterface $dataProvider)**:
may be required for initialization the model data provider, for example to increase the page size in the CRUD list
* **applyScopes(ActiveQuery $q)**: to initialize the query, for example, to add new conditions or selections.
* **attach()**: for the need to do something before connecting the plug-in to the model
* and methods for intercepting the events of the same name ActiveRecord
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