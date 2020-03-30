<?php


namespace execut\crudFields;


use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \yii\base\Model;

class LinkRenderer
{
    protected $nameAttribute = null;
    protected ?string $idAttribute = null;
    protected ?Model $model = null;

    protected $url = null;
    protected ?string $label = null;

    public function __construct(Model $model = null, $nameAttribute = null, string $idAttribute = null, string $label = null, $url = null)
    {
        $this->nameAttribute = $nameAttribute;
        $this->idAttribute = $idAttribute;
        $this->model = $model;
        $this->url = $url;
        $this->label = $label;
    }

    public function setNameAttribute($name) {
        $this->nameAttribute = $name;

        return $this;
    }

    public function getNameAttribute() {
        $name = $this->nameAttribute;

        return $name;
    }

    public function setIdAttribute($name) {
        $this->idAttribute = $name;

        return $this;
    }

    public function getIdAttribute() {
        return $this->idAttribute;
    }

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;

        return $this;
    }

    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getLabel() {
        return $this->label;
    }

    public function render() {
        if ($this->getModel() === null) {
            throw new Exception('Model is required for render');
        }

        if ($this->getNameAttribute() === null) {
            throw new Exception('nameAttribute is required for render');
        }

        $model = $this->model;
        $value = ArrayHelper::getValue($model, $this->nameAttribute);
        if ($value === null) {
            if ($model->hasProperty($this->idAttribute)) {
                return ArrayHelper::getValue($model, $this->idAttribute);
            }
        }

        if ($this->url === null) {
            return $value;
        }

        if ($label = $this->label) {
            $title = $label . ' - п';
        } else {
            $title = 'П';
        }

        $title .= 'ерейти к редактированию';

        return $value . '&nbsp;' . Html::a('>>>', Url::to($this->url), ['title' => $title]);
    }
}