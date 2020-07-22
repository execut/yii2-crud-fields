# Примеры использования
Yii2 CRUD fields покажет все свои преимущества только после того, как вы увидите как легко и быстро можно создавать
админки с его помощью. Для этого давайте разберём пару примеров.

## Простой пример использования
Допустим у нас возникла задача создать CRUD для управления простой моделью Book с двумя полями: id и name.

Для сравнения уже был написан точно такой-же CRUD без использования Yii2 CRUD fields в следующем пакете
[execut/yii2-books-native](https://github.com/execut/yii2-books-native). Весь функционал, который там реализован 
перечислен в [этом списке](https://github.com/execut/yii2-books-native/blob/master/docs/guide-ru/implemented-functionality.md).
Для демонстрации эффективности использования Yii2 CRUD fields мы будем улучшать этот пример путём подмены его моделей на
доработанные с помощью CRUD fields.
Вот перечень того, что из этого списка автоматизирует Yii2 CRUD fields:
1. Задание в модели правил валидации для сценария search путём объявления Book::rules()
1. Формирование ActiveQuery на основе параметров фильтрации
1. Настройка ActiveDataProvider для списка записей
1. Формирование настроек колонок списка
1. Задание правил валидации модели по сценарию form путём добавления Simple::rules()
1. Формирование настроек аттрибутов для формы редактирования

Уже доработанный с помощью Yii2 CRUD fields CRUD расположен в ещё одном пакете [execut/yii2-books](https://github.com/execut/yii2-books)
и все дальнейшие примеры будут ссылаться на него.

Автоматизация этих пунктов достигается путём подключения к модели [Book](https://github.com/execut/yii2-books/blob/master/models/Book.php) характеристики
[execut\crudFields\Behavior](Behavior.php) и черты [execut\crudFields\BehaviorStub](BehaviorStub.php):
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

В результате у нас появляются два поля, которые можно проверить unit-тестом [BookTest](https://github.com/execut/yii2-books/blob/master/tests/unit/models/BookTest.php):
```php
$book = new Book();
$field = $book->getField('id');
$this->assertInstanceOf(\execut\crudFields\fields\Id::class, $field);

$field = $book->getField('name');
$this->assertInstanceOf(\execut\crudFields\fields\StringField::class, $field);
$this->assertTrue($field->required);
```

Модель Book научилась делать всё от неё необходимое для CRUD-а автоматически:
```php
$model = new Book();
echo 'Правила валидации для сценария search и edit';
var_dump($model->rules());
echo 'Формирование ActiveQuery на основе параметров фильтрации и настройка ActiveDataProvider';
var_dump($model->search());
echo 'Формирование настроек колонок списка для \yii\grid\GridView';
var_dump($model->getGridColumns());
echo 'Формирование настроек формы создания\редактирования для \kartik\detail\DetailView';
var_dump($model->getFormFields());
```

Чтобы увидеть преимущества yii2-crud-fields на примере execut/yii2-books-native, необходимо его доработать используя yii2-crud-fields:
1. Установите в свой проект [пример CRUD execut/yii2-books-native](https://github.com/execut/yii2-books-native).
1. Замените в нём нативную модель [\execut\booksNative\models\Book](https://github.com/execut/yii2-books-native/blob/master/models/Book.php) на доработанную [\execut\books\models\Book](https://github.com/execut/yii2-books/blob/master/models/Book.php)
путём её подмены в конфигурации приложения:
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
3. В результате останется тот-же самый CRUD, но с двухкратно упрощённой моделью:
[Было 85 строк](https://github.com/execut/yii2-books-native/blob/master/models/Book.php), а [стало 37](https://github.com/execut/yii2-books/blob/master/models/Book.php).
![Список книг](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide-ru/i/books-list.jpg)
![Форма редактирования книг](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide-ru/i/books-form.jpg)

Большинство остальных пунктов тоже можно автоматизировать путём использования другого компонента [execut/yii2-crud](https://github.com/execut/yii2-crud)
, что ещё больше сократит дублирование кода между вашими CRUD-ами. Обращайтесь к его [документации](https://github.com/execut/yii2-crud), чтобы понять как это сделать.

Чем разнообразней и больше полей нашей CRUD-модели, тем жирнее и сложнее она становится. С каждым новым типом поля необходимо
писать и дублировать логику по проверке, поиску, выводу колонок и формированию полей формы модели. Yii2 CRUD fields позволяет
минимизировать такие затраты. Чтобы увидеть это в деле, давайте разберём более сложный пример использования.

## Сложный пример использования
Допустим, у наших книг есть авторы и для них нужны следующие поля:

Поле | Тип | Обязательное
-----|-----|-------------
Идентификатор | Id | +
Фамилия | Строка до 255 символов | +
Имя | Строка до 255 символов | +
Кратное описание| Текст | -
Биография|WYSIWYG редактор |  -
Дата рождения|Дата|-
Известность| Выпадающий список с элементами: Низкая, Средняя, Высокая|-
Электронная почта|Email|-
Изображение|Изображение с превьюшкой|-
Основная книга|Выпадающий список с данными из раздела Books|-
Книги|Мультивыпадающий список с данными из раздела Books|-
Дата создания|Дата и время (только для чтения)|+
Дата обновления|Дата и время (только для чтения)|-

Я попытался частично реализовать функционал подобного CRUD-a в виде нативного кода Yii2 в
[этом файле](https://github.com/execut/yii2-books-native/blob/master/models/Author.php).
Чтобы пример корректно заработал необходима поддержка в вашем PHP того формата картинок, который вы хотите загружать в CRUD.

Поскольку при написании нативного примера я устал копировать уже давно написанный код yii2-crud-fields, я намеренно
не сделал некоторые поля (Краткое описание, Биография, Дата рождения, Известность, Электронная почта, Книги), но даже этого кода оказалось больше,
чем в полной реализации с помощью yii2-crud-fields.

Далее я реализовал уже полный функционал подобного CRUD-а в [модели Author](https://github.com/execut/yii2-crud-fields/example/models/Author.php). Чтобы её
установить в пример CRUD execut/yii2-books-native необходимо:
1. Установить в свой проект [пример CRUD execut/yii2-books-native](https://github.com/execut/yii2-books-native).
1. Переключить в нём модель Author [\execut\booksNative\models\Author](https://github.com/execut/yii2-books-native/blob/master/models/Book.php) на доработанную [\execut\books\models\Book](https://github.com/execut/yii2-books/blob/master/models/Author.php)
путём её подмены в конфигурации приложения:
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
3. В результате мы написали в три раза быстрее более функциональный и компактный CRUD:
[Было 370 строк](https://github.com/execut/yii2-books-native/blob/master/models/Author.php), а [стало 116](https://github.com/execut/yii2-crud-fields/example/models/Author.php).
![Список авторов](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide-ru/i/authors-list.jpg)
![Форма редактирования авторов](https://raw.githubusercontent.com/execut/yii2-crud/master/docs/guide-ru/i/authors-form.jpg)