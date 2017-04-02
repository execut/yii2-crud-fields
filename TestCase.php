<?php
/**
 */

namespace execut\crudFields;


class TestCase extends \yii\codeception\TestCase
{
    public $appConfig = [
        'id' => 'actions-test',
        'basePath' => __DIR__ . '/../../../',
        'components' => [
            'assetManager' => [
                'basePath' => __DIR__ . '/tests/assets/',
            ],
        ],
    ];
}