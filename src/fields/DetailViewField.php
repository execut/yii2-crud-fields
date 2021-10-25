<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use execut\crudFields\fields\detailViewField\addon\AddonInterface;
use kartik\detail\DetailView;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class for wrapping \kartik\detail\DetailView fields configs
 * @see DetailView
 * @package execut\crudFields\fields
 */
class DetailViewField
{
    /**
     * @var array|null Source field configuration array
     */
    protected $fieldConfig = null;
    /**
     * @var string|null Model attribute name
     */
    protected ?string $attribute = null;
    /**
     * @var bool|callable Field is display only. Callback is supported
     */
    protected $displayOnly;
    /**
     * @var AddonInterface Addon of field
     */
    protected ?AddonInterface $addon = null;
    /**
     * @var bool Is hidden field flag
     */
    protected bool $isHidden = false;
    public function __construct($fieldConfig = [], string $attribute = null, $displayOnly = false, AddonInterface $addon = null)
    {
        $this->fieldConfig = $fieldConfig;
        $this->attribute = $attribute;
        $this->displayOnly = $displayOnly;
        $this->addon = $addon;
    }

    /**
     * Get field addon
     * @return AddonInterface
     */
    public function getAddon(): ?AddonInterface
    {
        return $this->addon;
    }

    /**
     * Set field addon
     * @param AddonInterface|null $addon
     */
    public function setAddon(?AddonInterface $addon): void
    {
        $this->addon = $addon;
    }

    /**
     * Get field config
     * @return array|null
     */
    public function getFieldConfig()
    {
        return $this->fieldConfig;
    }

    /**
     * Set field config
     * @param array|null $fieldConfig
     */
    public function setFieldConfig($fieldConfig): void
    {
        $this->fieldConfig = $fieldConfig;
    }

    /**
     * Set model attribute name
     *
     * @param string|null $attribute
     */
    public function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * Get model attribute name
     * @return string|null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set is display only. Callback is supported
     * @param callable|bool $displayOnly
     */
    public function setDisplayOnly($displayOnly): void
    {
        $this->displayOnly = $displayOnly;
    }

    /**
     * Get calculated is display only flag
     * @return bool
     */
    public function getDisplayOnly()
    {
        if (is_callable($this->displayOnly)) {
            return call_user_func($this->displayOnly);
        }

        return $this->displayOnly;
    }

    /**
     * Get array of field config for DetailView from model instance
     * @param Model null $model
     * @return array|bool
     */
    public function getConfig($model = null)
    {
        $field = $this->getFieldConfig();
        if (is_callable($field)) {
            $field = $field($model, $this);
        }

        if ($field === false) {
            return false;
        }

        if ($this->isHidden) {
            $field = ArrayHelper::merge($field, [
                'rowOptions' => [
                    'class' => 'hide',
                ]
            ]);
        }

        if ($model) {
            $field['viewModel'] = $model;
            $field['editModel'] = $model;
        }

        if (($attribute = $this->getAttribute()) !== null) {
            $field['attribute'] = $attribute;
        }

        $displayOnly = $this->getDisplayOnly();
        if ($displayOnly) {
            $field['displayOnly'] = true;
        }

        if (!empty($this->addon)) {
            $field['fieldConfig'] = [
                'addon' => $this->addon->getConfig(),
            ];
        }

        return $field;
    }

    /**
     * Hide field
     * @return $this
     */
    public function hide()
    {
        $this->isHidden = true;
        return $this;
    }

    /**
     * Show field
     * @return $this
     */
    public function show()
    {
        $this->isHidden = false;
        return $this;
    }

    public function getIsHidden()
    {
        return $this->isHidden;
    }
}
