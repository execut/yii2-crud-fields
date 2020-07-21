<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\relation;

/**
 * Class for make relations urls
 * @package execut\crudFields\relation
 */
class UrlMaker
{
    /**
     * @var array|null Relation CRUD url
     */
    protected $url = null;
    /**
     * @var array|null Relation update url
     */
    protected $updateUrl = null;
    /**
     * @var boolean Is no render link
     */
    protected $isNoRenderRelationLink = null;

    /**
     * UrlMaker constructor.
     * @param array $url
     * @param array $updateUrl
     * @param boolean $isNoRenderRelationLink
     */
    public function __construct($url = null, $updateUrl = null, $isNoRenderRelationLink = null)
    {
        $this->url = $url;
        $this->updateUrl = $updateUrl;
        $this->isNoRenderRelationLink = $isNoRenderRelationLink;
    }

    /**
     * Returns url
     * @return array|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns update url
     * @return array|null
     */
    public function getUpdateUrl()
    {
        return $this->updateUrl;
    }

    /**
     * Returns is no render link
     * @return boolean
     */
    public function getIsNoRenderRelationLink()
    {
        return $this->isNoRenderRelationLink;
    }

    /**
     * Make relation link from model and key attribute
     * @param \yii\base\Model $model Related model
     * @param string|array $keyAttribute Relation attributes
     * @return array|null
     */
    public function make(\yii\base\Model $model, $keyAttribute)
    {
        if ($this->isNoRenderRelationLink) {
            return null;
        }

        if ($this->updateUrl !== null) {
            return $this->updateUrl;
        }

        $url = $this->url;
        if ($url === null) {
            return null;
        }

        if (!is_array($url)) {
            $url = [$url];
        } else {
            $url[0] = str_replace('/index', '', $url[0]) . '/update';
        }

        if (!array_key_exists('id', $url)) {
            if (is_string($keyAttribute) && !$model->hasProperty($keyAttribute)) {
                return null;
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
