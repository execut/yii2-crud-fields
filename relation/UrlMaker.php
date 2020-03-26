<?php


namespace execut\crudFields\relation;


class UrlMaker
{
    protected $url = null;
    protected $updateUrl = null;
    protected $isNoRenderRelationLink = null;
    public function __construct($url = null, $updateUrl = null, $isNoRenderRelationLink = null)
    {
        $this->url = $url;
        $this->updateUrl = $updateUrl;
        $this->isNoRenderRelationLink = $isNoRenderRelationLink;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getUpdateUrl() {
        return $this->updateUrl;
    }

    public function getIsNoRenderRelationLink() {
        return $this->isNoRenderRelationLink;
    }

    public function make(\yii\base\Model $model, $keyAttribute) {
        if ($this->isNoRenderRelationLink) {
            return;
        }

        if ($this->updateUrl !== null) {
            return $this->updateUrl;
        }

        $url = $this->url;
        if ($url === null) {
            return;
        }

        if (!is_array($url)) {
            $url = [$url];
        } else {
            $url[0] = str_replace('/index', '', $url[0]) . '/update';
        }

        if (!array_key_exists('id', $url)) {
            if (is_string($keyAttribute) && !$model->hasProperty($keyAttribute)) {
                return;
            }

            $pkValue = $model->$keyAttribute;
            if (is_array($pkValue)) {
                $url = array_merge($url, $pkValue);
            } else {
                $url = array_merge($url, ['id' => $pkValue]);
            }
        }
        return $url;
    }
}