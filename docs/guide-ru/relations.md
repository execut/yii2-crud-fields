# Cвязи ActiveRecord
CRUD fields позволяет работать как с обычными связями модели, так и связями, объявленными напрямую в поле. Это сделано
для возможности динамичного управления связями модели и с ними связанными полями. В примере выше используются оба способа
объявления и взаимодействия со связями:
```php
    'mainBook' => [
        'class' => HasOneSelect2::class,
        'attribute' => 'main_book_id',
        'relation' => 'mainBook',
        'relationQuery' => $this->hasOne(Book::class, ['id' => 'main_book_id']),
        'url' => [
            '/crudExample/books'
        ],
    ],
    'books' => [
        'class' => HasManySelect2::class,
        'attribute' => 'books',
        'relation' => 'books',
        'relationQuery' => $this->hasMany(Book::class, ['id' => 'example_book_id'])->via('vsBooks'),
        'url' => [
            '/crudExample/books'
        ],
    ],
```

За счёт этой конфигурации со связями mainBook и books можно работать как с обычными связями в Yii2, например:
```php
// Загружать через ActiveQuery:
$authors = \execut\books\models\Author::find()->with([
    'mainBook',
    'books',
]);
// ...и получать к ним доступ напрямую:
foreach ($authors as $author) {
    if ($author->mainBook) {
        var_dump('Главная книга: ' . $author->mainBook->name);
    }

    foreach ($author->books as $book) {
        var_dump($book->name);
    }
}
```