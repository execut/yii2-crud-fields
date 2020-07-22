# eXeCUT Yii2 CRUD fields
This component allows you to automate many processes that occur in working with models, thereby reducing code
duplication, and hence the total time spent:
* Writing validation rules for fields of the same type
* Writing Getters to Declare Various Relationships with Other Models
* Validating and Editing Linked Records
* Setting up the edit form for the model and its associated records
* Customizing the Model Record List
* Translating field names
* Adds the ability to extend models by a third-party module without adding new dependencies
* Simplifies the process of unit testing models


For license information check the [LICENSE](LICENSE.md)-file.

English documentation is at [docs/guide/README.md](https://github.com/execut/yii2-crud-fields/blob/master/docs/guide/README.md).

Русская документация здесь [docs/guide-ru/README.md](https://github.com/execut/yii2-crud-fields/blob/master/docs/guide-ru/README.md).

[![Latest Stable Version](https://poser.pugx.org/execut/yii2-crud-fields/v/stable.png)](https://packagist.org/packages/execut/yii2-crud-fields)
[![Total Downloads](https://poser.pugx.org/execut/yii2-crud-fields/downloads.png)](https://packagist.org/packages/execut/yii2-crud-fields)
[![Build Status](https://travis-ci.com/execut/yii2-crud-fields.svg?branch=master)](https://travis-ci.com/execut/yii2-crud-fields)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require execut/yii2-crud-fields
```

or add

```
"execut/yii2-crud-fields": "dev-master"
```

to the require section of your `composer.json` file.

Usage
----

For example, the following few lines of code in a model:

```php
namespace execut\books\models;
class Book extends \yii\db\ActiveRecord {
    use \execut\crudFields\BehaviorStub;
    public function behaviors() {
        return [
            \execut\crudFields\Behavior::KEY => [
                'class' => \execut\crudFields\Behavior::class,
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
}
```

 will make all required for CRUD:
 ```php
 $model = new Book();
 echo 'Validation rules for the search and edit scenario';
 var_dump($model->rules());
 echo 'Forming ActiveQuery based on filtering parameters and configuring ActiveDataProvider';
 var_dump($model->search());
 echo 'Formation of list columns settings';
 var_dump($model->getGridColumns());
 echo 'Formation of settings for the creation/editing form';
 var_dump($model->getFormFields());
 ```

![Books CRUD list](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/books-list.jpg)
![Books CRUD form](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/books-form.jpg)

If we compare the implementation of such a model with a model without extension, we can see that the amount of code has changed in a positive direction:

[Model on native Yii2 (85 lines)](https://github.com/execut/yii2-books-native/blob/master/models/Book.php) vs [Model on CRUD fields (36 lines)](https://github.com/execut/yii2-books/blob/master/models/Book.php)

Or more strong example with books authors:

[Model on native Yii2 (370 lines)](https://github.com/execut/yii2-books-native/blob/master/models/Author.php) vs [Model on CRUD fields (116 lines)](https://github.com/execut/yii2-books/blob/master/models/Author.php)

![Authors CRUD list](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/authors-list.jpg)
![Authors CRUD form](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/authors-form.jpg)

For more details please refer to the documentation [docs/guide/README.md](https://github.com/execut/yii2-crud-fields/blob/master/docs/guide/README.md).

Для более подробной информации обращайтесь к документации [docs/guide-ru/README.md](https://github.com/execut/yii2-crud-fields/blob/master/docs/guide-ru/README.md).
