<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField;

use execut\crudFields\fields\DetailViewField;
use execut\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;

/**
 * DetailViewField for WYSIWYG HTML editor widget
 * @package execut\crudFields\fields\detailViewField
 */
class Editor extends DetailViewField
{
    /**
     * {@inheritdoc}
     */
    public function getConfig($model = null)
    {
        return array_merge([
            'type' => DetailView::INPUT_WIDGET,
            'format' => 'html',
            'widgetOptions' => [
                'class' => CKEditor::class,
                'preset' => 'full',
                'clientOptions' => [
                    'language' => \yii::$app ? \yii::$app->language : null,
                    'allowedContent' => true,
                ],
            ],
        ], parent::getConfig($model));
    }
}
