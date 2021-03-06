<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use kartik\grid\GridView;

class TestGridViewWidget extends GridView
{
    public static $isRunned = false;
    public static $test = null;
    public $dataProvider = null;
    public static $factDataProvider = null;
    public static $factConfig = null;
    public function __construct($config = [])
    {
        parent::__construct($config);
        self::$factConfig =  $config;
    }

    public function init()
    {
    }

    public function run()
    {
        self::$isRunned = true;
        self::$factDataProvider = $this->dataProvider;
        return 'test';
    }

    public function setTest(string $test)
    {
        self::$test = $test;
    }
}
