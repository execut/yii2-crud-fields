<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use yii\base\Event;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\imagine\BaseImage;
use \yii\imagine\Image as ImagineImage;

/**
 * Field for loading an image and creating its thumbnails
 * @package execut\crudFields
 */
class Image extends File
{
    /**
     * {@inheritdoc}
     */
    public $allowedExtensions = [
        'jpg',
        'jpeg',
        'gif',
        'bmp',
        'png',
    ];
    /**
     * @var string Attribute for data of preview image
     */
    public $previewDataAttribute = null;
    /**
     * @var string|array Url of preview image
     */
    public $previewRoute = null;
    /**
     * @var array Thumbnails images sizes configuration.
     * Example:
     * [
     *     'image_211' => [
     *         'width' => 211,
     *         'mode' => ImageInterface::THUMBNAIL_OUTBOUND,
     *      ],
     *      'image_606' => [
     *          'width' => 606,
     *          'mode' => ImageInterface::THUMBNAIL_OUTBOUND,
     *      ],
     * ],
     */
    public $sizes = [];

    /**
     * {@inheritdoc}
     */
    public function attach()
    {
        parent::attach();
        $this->attachToModels();
    }

    /**
     * Attach handlers to model events
     */
    public function attachToModels()
    {
        $model = $this->model;
        $modelClass = get_class($model);
        Event::on($modelClass, ActiveRecord::EVENT_BEFORE_INSERT, function ($e) {
            $file = $e->sender;
            $this->onBeforeFileSave($file);
        });
        Event::on($modelClass, ActiveRecord::EVENT_BEFORE_UPDATE, function ($e) {
            $file = $e->sender;
            $this->onBeforeFileSave($file);
        });
    }

    /**
     * Generate thumbnails for image
     * @param Model $file
     */
    public function onBeforeFileSave($file)
    {
        $sizes = $this->sizes;
        $dataAttribute = $this->dataAttribute;
        $data = $file->$dataAttribute;
        if (!$data) {
            return;
        }

        if (is_string($data)) {
            $tempFile = tempnam('/tmp', 'temp_');
            file_put_contents($tempFile, $data);
            $data = fopen($tempFile, 'r+');
        }

        if (stream_get_contents($data) == '') {
            return;
        }

        fseek($data, 0);
        $sourceImage = @ImagineImage::getImagine()->read($data);

        foreach ($sizes as $sizeName => $size) {
            $thumbnailAttributeName = $sizeName;
            if (!empty($size['width'])) {
                $width = $size['width'];
                if ($width < 0) {
                    $originalWidgth = $sourceImage->getSize()->getWidth();
                    if (-$originalWidgth < $width * 4) {
                        $width = $sourceImage->getSize()->getWidth() + $width;
                    } else {
                        $width = $originalWidgth;
                    }
                }
            } else {
                $width = null;
            }

            if (!empty($size['height'])) {
                $height = $size['height'];
                if ($height < 0) {
                    $originalHeight = $sourceImage->getSize()->getHeight();
                    if (-$originalHeight < $height * 4) {
                        $height = $sourceImage->getSize()->getHeight() + $height;
                    } else {
                        $height = $originalHeight;
                    }
                }
            } else {
                $height = null;
            }

            if (!empty($size['mode'])) {
                $mode = $size['mode'];
            } else {
                $mode = ImageInterface::THUMBNAIL_INSET;
            }

            BaseImage::$thumbnailBackgroundAlpha = 0;
            $image = ImagineImage::thumbnail($sourceImage, $width, $height, $mode);

            if (!empty($size['watermark'])) {
                $watermark = fopen($size['watermark'], 'r+');
                $watermark = ImagineImage::thumbnail($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight(), ManipulatorInterface::THUMBNAIL_OUTBOUND);
                $watermark = ImagineImage::crop($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight());

                $image = ImagineImage::watermark($image, $watermark);
            }

            $fileName = tempnam(sys_get_temp_dir(), 'test');
            $extensionAttribute = $this->fileExtensionAttribute;
            $image->save($fileName, [
                'format' => $file->$extensionAttribute,
            ]);

            $thumbData = fopen($fileName, 'r+');
            $file->$thumbnailAttributeName = $thumbData;
        }

        fseek($data, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getFields($isWithRelationsFields = true)
    {
        $value = $this->getValueCallback();

        return ArrayHelper::merge(parent::getFields($isWithRelationsFields), [
            'preview' => [
                'label' => $this->translateAttribute($this->attribute . 'Preview'),
                'format' => 'raw',
                'displayOnly' => true,
                'value' => function ($form, $widget) use ($value) {
                    return $value($widget->model);
                },
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getColumns()
    {
        $value = $this->getValueCallback();
        return ArrayHelper::merge(parent::getColumns(), [
            $this->attribute . 'Preview' => [
                'label' => $this->translateAttribute($this->attribute . 'Preview'),
                'filter' => false,
                'format' => 'raw',
                'value' => $value,
            ]
        ]);
    }

    /**
     * @return \Closure
     */
    protected function getValueCallback(): \Closure
    {
        $value = function ($row) {
            $extensionAttribute = $this->fileExtensionAttribute;

            return Html::a(Html::img([$this->previewRoute, 'id' => $row->id, 'extension' => strtolower($row->$extensionAttribute), 'dataAttribute' => $this->previewDataAttribute]), [
                $this->previewRoute,
                'id' => $row->id,
                'extension' => strtolower($row->$extensionAttribute),
            ]);
        };
        return $value;
    }
}
