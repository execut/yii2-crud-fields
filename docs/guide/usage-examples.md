# Usage examples
Yii2 CRUD fields will show all their advantages only after you see how easy and fast you can create
admin panel with its help. To do this, let's look at a couple of examples.

## Simple example of use
Let's say we have a task to create a CRUD to manage a simple Book model with two fields: id and name.

For comparison exactly the same CRUD has already been written on native Yii2 in the following package
[execut/yii2-books-native](https://github.com/execut/yii2-books-native). All the functionality that is implemented there
listed in [this list](https://github.com/execut/yii2-books-native/blob/master/docs/guide-ru/implemented-functionality.md).
To demonstrate the effectiveness of using Yii2 CRUD fields, we will improve this example by replacing its models with
modified using CRUD fields.
Here is a list of what Yii2 CRUD fields automates from this list:
1. Setting validation rules for the search scenario in the model by declaring ```Book::rules()```
1. Configuring of ActiveQuery based on filtering parameters
1. Configuring ActiveDataProvider for list of records
1. Configuring of list column settings
1. Setting the model validation rules for the form script by adding ```Simple::rules()```
1. Configuring of settings of attributes for the edit form

CRUD fields, already modified with Yii2, is located in another package [execut/yii2-books](https://github.com/execut/yii2-books)
 and all further examples will refer to it.

The automation of these points is achieved by connecting the behavior to the model [Book](https://github.com/execut/yii2-books/blob/master/models/Book.php) behavior
[execut\crudFields\Behavior](Behavior.php) and trait [execut\crudFields\BehaviorStub](BehaviorStub.php):
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

As a result, we have two fields that can be checked with a unit test [BookTest](https://github.com/execut/yii2-books/blob/master/tests/unit/models/BookTest.php):
```php
$book = new Book();
$field = $book->getField('id');
$this->assertInstanceOf(\execut\crudFields\fields\Id::class, $field);

$field = $book->getField('name');
$this->assertInstanceOf(\execut\crudFields\fields\StringField::class, $field);
$this->assertTrue($field->required);
```

The Book model has learned to do everything necessary for a CRUD from it automatically:
```php
$model = new Book();
echo 'Validation rules for the search and edit scenario';
var_dump($model->rules());
echo 'ActiveQuery and ActiveDataProvider configuration based on filter parameters';
var_dump($model->search());
echo 'Generation of list column settings for \yii\grid\GridView';
var_dump($model->getGridColumns());
echo 'Generating form settings for \kartik\detail\DetailView';
var_dump($model->getFormFields());
```

To see the benefits of yii2-crud-fields using execut/yii2-books-native as an example, you need to modify it using yii2-crud-fields:
1. Install in your project [example of CRUD execut/yii2-books-native](https://github.com/execut/yii2-books-native).
1. Replace the native model [execut\booksNative\models\Book](https://github.com/execut/yii2-books-native/blob/master/models/Book.php) with the modified [execut\books\models\Book](https://github.com/execut/yii2-books/blob/master/models/Book.php)
   by redefine it in the application configuration:
```php
return [
    'bootstrap' => [
         'booksNative' => [
            'class' => \execut\booksNative\bootstrap\Console::class,
            'moduleConfig' => [
                'bookModelClass' => \execut\books\models\Book::class,
            ]
        ],
    ],
];
```
3. As a result, the same CRUD will remain, but with a two-fold simplified model:
[There were 85 lines](https://github.com/execut/yii2-books-native/blob/master/models/Book.php), but [now it is 37](https://github.com/execut/yii2-books/blob/master/models/Book.php).
![Books CRUD list](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/books-list.jpg)
![Books CRUD form](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/books-form.jpg)

Most of the rest of the items can also be automated by using another component [execut/yii2-crud](https://github.com/execut/yii2-crud)
, which will further reduce code duplication between your CRUDs.
Please refer to its [documentation](https://github.com/execut/yii2-crud) to understand how to do this.

The more varied and more fields in our CRUD model, the fatter and more complex it becomes.
With each new type of field, it is necessary to write and duplicate logic for validating, searching, displaying columns and forming model form fields.
Yii2 CRUD fields allows you to minimize such costs.
To see this in action, let's break down a more complex use case.

## Complex use case
Let's say our books have authors and they need the following fields:

Field | Type | Required
-----|-----|-------------
ID | Id | +
Surname | String up to 255 characters | +
Name | String up to 255 characters | +
Short description | Text | -
Biography | WYSIWYG editor | -
Date of birth | Date | -
Popularity | Drop-down list with elements: Low, Medium, High | -
Email | Email | -
Image | Image with preview | -
Main book | Drop-down list with data from the Books section | -
Books | Multi-dropdown list with data from the Books section | -
Date created | Date and time (read-only) | +
Update date | Date and time (read-only) | -

I tried to partially implement the functionality of a similar CRUD in the form of native Yii2 code in [this file](https://github.com/execut/yii2-books-native/blob/master/models/Author.php).
For the example to work correctly, you need to support in your PHP the image format that you want to load in CRUD.

Since, when writing a native example, I got tired of copying the code yii2-crud-fields, I intentionally
did not make some fields (Short description, Biography, Date of birth, Popularity, Email, Books), but even this code turned out to be more,
than in the full implementation with yii2-crud-fields.

Further, I have already implemented the full functionality of such a CRUD in the [Author model](https://github.com/execut/yii2-crud-fields/example/models/Author.php).
To install it in the example CRUD execut/yii2-books-native you need:
1. Install in your project [example of CRUD execut/yii2-books-native](https://github.com/execut/yii2-books-native).
1. Replace the native model Author [\execut\booksNative\models\Author](https://github.com/execut/yii2-books-native/blob/master/models/Book.php) на доработанную [\execut\books\models\Book](https://github.com/execut/yii2-books/blob/master/models/Author.php)
by changing it in the application configuration:
```php
return [
    'bootstrap' => [
         'crudExample' => [
            'class' => \execut\booksNative\bootstrap\Console::class,
            'moduleConfig' => [
                'authorModelClass' => \execut\books\models\Author::class,
            ]
        ],
    ],
];
```
3. As a result, we wrote a more functional and compact CRUD three times faster:
[There were 370 lines](https://github.com/execut/yii2-books-native/blob/master/models/Author.php), but [now it is 116](https://github.com/execut/yii2-crud-fields/example/models/Author.php).
![Authors CRUD list](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/authors-list.jpg)
![Authors CRUD form](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide/i/authors-form.jpg)