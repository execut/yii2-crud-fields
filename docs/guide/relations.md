# ActiveRecord relations
CRUD fields allows you to work with both regular model relationships and relationships declared directly in the field.
This is done to allow dynamic configuring of model relationships and related fields.
The example above uses both ways to declare and interact with relationships:
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

Due to this configuration, you can work with the mainBook and books relationships as with regular relationships in Yii2.
For example:
```php
// Load via ActiveQuery:
$authors = \execut\books\models\Author::find()->with([
    'mainBook',
    'books',
]);
// ...and access them directly:
foreach ($authors as $author) {
    if ($author->mainBook) {
        var_dump('Главная книга: ' . $author->mainBook->name);
    }

    foreach ($author->books as $book) {
        var_dump($book->name);
    }
}
```