<?php
use execut\crudFields\widgets\Select2ExecutAsset;
use kartik\select2\Select2Asset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\jui\JuiAsset;

$params = [];

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
    ],
//    'vendorPath' => dirname(__DIR__) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'baseUrl' => ''
        ],
        'view' => [
            'class' => \yii\web\View::class,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../../tests/assets/',
            'bundles' => [
                JuiAsset::class => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                \execut\crudFields\widgets\HasRelationDropdownAsset::class => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                BootstrapPluginAsset::class => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                Select2ExecutAsset::class => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                Select2Asset::class => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                'yii\\web\\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                'yii\\bootstrap\\BootstrapAsset' => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                'kartik\\daterange\\DateRangePickerAsset' => [
                    'depends' => [],
                    'js' => [],
                    'sourcePath' => null,
                    'basePath' => null,
                    'baseUrl' => null,
                    'css' => [],
                    'jsOptions' => [],
                    'cssOptions' => [],
                    'publishOptions' => [],
                ],
            ],
        ],
//        'view' => [
//            'class' => \yii\web\View::class,
//        ],
//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                ],
            ]
        ],
    ],
    'params' => $params,
];
