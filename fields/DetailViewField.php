<?php


namespace execut\crudFields\fields;


use execut\autosizeTextarea\TextareaWidget;
use execut\iconsCheckboxList\IconsCheckboxList;
use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;

class DetailViewField
{
    protected $fieldConfig = null;
    protected $attribute = null;
    protected $displayOnly = null;
    protected $model = null;
    public function __construct($model = null, $fieldConfig = null, $attribute = null, $displayOnly = null)
    {
        $this->fieldConfig = $fieldConfig;
        $this->attribute = $attribute;
        $this->displayOnly = $displayOnly;
    }

    /**
     * @return null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param null $model
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @return null
     */
    public function getFieldConfig()
    {
        return $this->fieldConfig;
    }

    /**
     * @param null $fieldConfig
     */
    public function setFieldConfig($fieldConfig): void
    {
        $this->fieldConfig = $fieldConfig;
    }

    /**
     * @param null $attribute
     */
    public function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @return null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param null $displayOnly
     */
    public function setDisplayOnly($displayOnly): void
    {
        $this->displayOnly = $displayOnly;
    }

    /**
     * @return null
     */
    public function getDisplayOnly()
    {
        return $this->displayOnly;
    }

    public function getConfig() {
        return [];
        return [
            /**
             * Field
             */
            'viewModel' => $this->model,
            'editModel' => $this->model,
            'attribute' => $this->attribute,
            'displayOnly' => $this->displayOnly,
            'valueColOptions' => [],

            /**
             * StringField
             */
            /** нет */

            /**
             * AutosizeTextarea
             */
            'type' => DetailView::INPUT_WIDGET,
            'options' => [
                'class' => 'form-control',
                'style' => 'height: 32px',
            ],
            'widgetOptions' => [
                'class' => TextareaWidget::class,
                'clientOptions' => [
                    'vertical' => true,
                    'horizontal' => false,
                ],
            ],

            /**
             * Boolean
             */
            'type' => DetailView::INPUT_CHECKBOX,
            'value' => function () {},
            /**
             * CheckboxList
             */
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $this->attribute,
            'label' => $this->getLabel(),
            'format' => 'raw',
            'value' => '',
            'widgetOptions' => [
                'class' => IconsCheckboxList::class,
                'items' => $items,
            ],

            /**
             * DropDown
             */
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => $attribute,
            'value' => function () use ($data, $value) {
                if (!empty($data[$value])) {
                    return $data[$value];
                }
            },
            'items' => $data,

            /**
             * Editor
             */
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

            /**
             * Select2
             */
            'type' => $type,
//            'widgetClass' => Select2::class,
            'value' => $this->getRelationObject()->getColumnValue($this->model),
            'format' => 'raw',
            'widgetOptions' => [
                'addons' => []
            ],
            'fieldConfig' => [
                //                'template' => "{input}$createButton\n{error}\n{hint}",
            ],
            'displayOnly' => $this->getIsRenderRelationFields(),
            'rowOptions' => $rowOptions,
        ];
    }
}