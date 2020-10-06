<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\tests\unit\fields\detailViewField;

use execut\crudFields\fields\detailViewField\Editor;
use execut\crudFields\TestCase;
use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;

class EditorTest extends \Codeception\Test\Unit
{
    public function testGetConfig()
    {
        $field = new Editor([], 'text');
        $this->assertEquals([
            'attribute' => 'text',
            'type' => DetailView::INPUT_WIDGET,
            'format' => 'html',
            'widgetOptions' => [
                'class' => CKEditor::class,
                'preset' => 'full',
                'clientOptions' => [
                    'allowedContent' => true,
                    'language' => 'en-US'
                ],
            ],
        ], $field->getConfig());
    }
}
