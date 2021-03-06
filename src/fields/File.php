<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\detail\DetailView;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * File upload field
 * @package execut\crudFields
 */
class File extends Field
{
    /**
     * @var string Attribute name for file name recording
     */
    public $fileNameAttribute = 'name';
    /**
     * @var string Attribute name for file extension
     */
    public $fileExtensionAttribute = 'extension';
    /**
     * @var string Attribute name for MIME-type
     */
    public $fileMimeTypeAttribute = 'mime_type';
    /**
     * @var string Attribute name for UploadedFile object
     * @see UploadedFile
     */
    public $attribute = 'dataFile';
    /**
     * @var string Attribute name to save file data
     */
    public $dataAttribute = 'data';
    /**
     * @var string Attribute name to write md5 hash of file
     */
    public $md5Attribute = 'file_md5';
    /**
     * @var string|array Url for downloading file. Used for rendering links.
     */
    public $downloadUrl = null;
    /**
     * @var string[] Allowed extensions list
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function applyScopes(ActiveQueryInterface $query)
    {
        if (!empty($this->model->{$this->md5Attribute})) {
            $query->andWhere([
                $this->md5Attribute => $this->model->{$this->md5Attribute},
            ]);
        }

        return parent::applyScopes($query); // TODO: Change the autogenerated stub
    }

    /**
     * Returns DetailView with file parameters
     * @param Model $model Model instance
     * @return string
     * @throws \Exception
     */
    public function getDisplayedValue($model)
    {
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            $this->attribute . 'File' => [[$this->attribute], 'file', 'skipOnEmpty' => true, 'checkExtensionByMimeType' => false, 'extensions' => implode(',', $this->allowedExtensions)],
            $this->fileNameAttribute . 'Default' => [[$this->fileNameAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->name;
                }

                return null;
            }, 'skipOnEmpty' => false,],
            $this->fileExtensionAttribute . 'Default' => [[$this->fileExtensionAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->extension;
                }

                return null;
            }],
            $this->md5Attribute . 'Default' => [[$this->md5Attribute], 'default', 'value' => function () {
                $dataAttribute = $this->dataAttribute;
                $data = $this->model->$dataAttribute;
                if (!empty($data)) {
                    if (is_resource($data)) {
                        $sourceData = $data;
                        $data = stream_get_contents($data);
                        fseek($sourceData, 0);
                    }

                    return md5($data);
                }

                return null;
            }],
            $this->dataAttribute . 'Safe' => [$this->dataAttribute, 'safe'],
        ];

        if ($this->fileMimeTypeAttribute) {
            $rules[$this->fileMimeTypeAttribute . 'Default'] = [[$this->fileMimeTypeAttribute], 'default', 'value' => function () {
                $value = $this->getValue();
                if (!empty($value)) {
                    return $value->type;
                }

                return null;
            }];
        }

        $parentRules = parent::rules();

        if (!$this->model->isNewRecord) {
            unset($parentRules['dataFilerequiredonFormAndDefault']);
        }

        return array_merge($parentRules, $rules); // TODO: Change the autogenerated stub
    }
}
