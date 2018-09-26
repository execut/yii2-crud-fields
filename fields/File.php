<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class File extends Field
{
    public $fileNameAttribute = 'name';
    public $fileExtensionAttribute = 'extension';
    public $fileMimeTypeAttribute = 'mime_type';
    public $attribute = 'dataFile';
    public $dataAttribute = 'data';
    public $md5Attribute = 'file_md5';
    public $downloadUrl = null;
    public $allowedExtensions = [
        'rar',
        'zip',
        'xls',
        'xlt',
        'xlsx',
        'xlsm',
        'xltx',
        'xltm',
        'ods',
        'ots',
        'slk',
        'xml',
        'csv',
        'txt',
        'gnumeric',
        'jpg',
        'jpeg',
        'gif',
        'bmp',
        'png',
    ];

    public function getField()
    {
        return ArrayHelper::merge(parent::getField(), [
            'type' => DetailView::INPUT_FILE,
            'value' => function () {
                return $this->getDisplayedValue($this->model);
            },
            'format' => 'raw',
        ]);
    }

    public function getFields($isWithRelationsFields = true)
    {
        return ArrayHelper::merge(parent::getFields($isWithRelationsFields), [
            $this->md5Attribute => [
                'type' => DetailView::INPUT_TEXT,
                'displayOnly' => true,
                'label' => \Yii::t('execut/' . $this->module, 'Md5 hash'),
                'attribute' => $this->md5Attribute,
            ],
        ]);
    }

    public function getColumns()
    {
        if ($this->downloadUrl !== null) {
            $nameValue = function ($row) {
                $url = $this->downloadUrl;
                $url[current($row->primaryKey())] = $row->primaryKey;

                return $row->{$this->fileNameAttribute} . '&nbsp;' . Html::a('', Url::to($url), ['class' => ' glyphicon glyphicon-download-alt']);
            };
        } else {
            $nameValue = null;
        }

        $columns = [
            $this->fileNameAttribute => [
                'label' => \Yii::t('execut/' . $this->module, 'Name'),
                'attribute' => $this->fileNameAttribute,
                'format' => 'raw',
                'value' => $nameValue,
            ],
            $this->md5Attribute => [
                'label' => \Yii::t('execut/' . $this->module, 'Md5 hash'),
                'attribute' => $this->md5Attribute,
            ],
            $this->fileExtensionAttribute => [
                'label' => \Yii::t('execut/' . $this->module, 'Extension'),
                'attribute' => $this->fileExtensionAttribute,
            ],
        ];

        if ($this->fileMimeTypeAttribute !== false) {
            $columns[$this->fileMimeTypeAttribute] = [
                'label' => \Yii::t('execut/' . $this->module, 'Mime-type'),
                'attribute' => $this->fileMimeTypeAttribute,
            ];
        }

        return $columns;
    }

    public function getDisplayedValue($model) {
        $attributes = [
            $this->fileNameAttribute,
            $this->fileExtensionAttribute,
            $this->md5Attribute
        ];

        if ($this->fileMimeTypeAttribute !== false) {
            $attributes[] = $this->fileMimeTypeAttribute;
        }

        return \yii\widgets\DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    public function rules()
    {
        $rules = [
            $this->attribute . 'File' => [[$this->attribute], 'file', 'skipOnEmpty' => true, 'checkExtensionByMimeType' => false, 'extensions' => implode(',', $this->allowedExtensions)],
            $this->fileNameAttribute . 'Default' => [[$this->fileNameAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->name;
                }
            }, 'skipOnEmpty' => false,],
            $this->fileExtensionAttribute . 'Default' => [[$this->fileExtensionAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->extension;
                }
            }],
            $this->md5Attribute . 'Default' => [[$this->md5Attribute], 'default', 'value' => function () {
                $dataAttribute = $this->dataAttribute;
                $data = $this->model->$dataAttribute;
                if (!empty($data)) {
                    if (is_resource($data)) {
                        $data = stream_get_contents($data);
                        fseek($this->model->$dataAttribute, 0);
                    }

                    return md5($data);
                }
            }],
            $this->dataAttribute . 'Safe' => [$this->dataAttribute, 'safe'],
        ];

        if ($this->fileMimeTypeAttribute) {
            $rules[$this->fileMimeTypeAttribute . 'Default'] = [[$this->fileMimeTypeAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->type;
                }
            }];
        }

        $parentRules = parent::rules();

        if (!$this->model->isNewRecord) {
            unset($parentRules['dataFilerequiredonFormAndDefault']);
        }

        return array_merge($parentRules, $rules); // TODO: Change the autogenerated stub
    }

    public function getLabel()
    {
        return parent::getLabel(); // TODO: Change the autogenerated stub
    }
}