<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use execut\crudFields\fields\detailViewField\addon\AddonInterface;
use yii\helpers\ArrayHelper;
class DetailViewField
{
    protected $fieldConfig = null;
    protected $attribute = null;
    protected $displayOnly = null;
    protected $addon = null;
    protected $isHidden = false;
    public function __construct($fieldConfig = [], $attribute = null, $displayOnly = false, AddonInterface $addon = null)
    {
        $this->fieldConfig = $fieldConfig;
        $this->attribute = $attribute;
        $this->displayOnly = $displayOnly;
        $this->addon = $addon;
    }

    /**
     * @return array
     */
    public function getAddon(): ?AddonInterface
    {
        return $this->addon;
    }

    /**
     * @param array $addons
     */
    public function setAddon(?AddonInterface $addon): void
    {
        $this->addon = $addon;
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

//    /**
//     * @param null $fieldConfig
//     */
//    public function addFieldConfig($fieldConfig): self
//    {
//        $this->fieldConfig = ArrayHelper::merge($this->fieldConfig, $fieldConfig);
//
//        return $this;
//    }

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
        if (is_callable($this->displayOnly)) {
            return call_user_func($this->displayOnly);
        }

        return $this->displayOnly;
    }

    public function getConfig($model = null) {
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
//            $field['hideIfEmpty'] = false;
        }

        if (!empty($this->addon)) {
            $field['fieldConfig'] = [
                'addon' => $this->addon->getConfig(),
            ];
        }

        return $field;
    }

    public function hide() {
        $this->isHidden = true;
        return $this;
    }

    public function show() {
        $this->isHidden = false;
        return $this;
    }
}