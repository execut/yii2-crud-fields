<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\depdrop\DepDrop;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\UnsetArrayValue;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneDepDrop extends HasOneSelect2
{
    public $depends = [];
    public $dependedAttribute = null;
    public $searchDataScopes = [];
    public function getField()
    {
        $field = parent::getField();
        if ($this->displayOnly) {
            return $field;
        }

        $widgetOptions = $field['widgetOptions'];
        unset($field['widgetOptions']);
        unset($widgetOptions['url']);
        unset($widgetOptions['isRenderLink']);
        unset($widgetOptions['pluginOptions']['ajax']);
//        if ($this->getValue()) {
//            $data = $this->getData();
//        } else {
        $data = $this->getData();
//        }

        return ArrayHelper::merge($field, [
            'type' => DetailView::INPUT_DEPDROP,
            'widgetOptions' => [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $data,
                'name' => $this->attribute,
                'value' => $this->value,
                'select2Options' => $widgetOptions,
                'pluginOptions' => [
                    'loadingText' => 'Загрузка...',
//                    'initialize' => true,
                    'nameParam' => 'text',
                    'allParam' => $this->dependedAttribute,
                    'ajaxSettings' => [
                        'method' => 'get',
                    ],
                    'url' => Url::to($this->url),
                    'depends' => $this->getDepends(),
                ],
            ],
        ]);
    }

    public function getColumn()
    {
        $column = parent::getColumn();
        if ($column == false) {
            return $column;
        }

        $column = ArrayHelper::merge($column, [
            'filterWidgetOptions' => [
                'showToggleAll' => true,
                'pluginOptions' => [
                    'ajax' => new UnsetArrayValue(),
                ],
                'url' => new UnsetArrayValue(),
                'data' => $this->getData(),
            ],
        ]);

        return $column;
    }

    public function getMultipleInputField()
    {
        throw new Exception(__METHOD__ . ' is not implemented');
    }

    protected function getDepends() {
        $result = [];
        foreach ($this->depends as $depend) {
            $result[] = Html::getInputId($this->model, $depend);
        }

        return $result;
    }

    public function getData() {
        $query = clone $this->getRelationObject()->getRelationQuery();
        $query->primaryModel = null;
        $isHas = false;
        $depends = [$this->depends[count($this->depends) - 1]];
        foreach ($depends as $depend) {
            if (!empty($this->model->$depend) && empty($this->model->errors[$depend])) {
                $isHas = true;
                if (!empty($this->searchDataScopes[$depend])) {
                    $this->searchDataScopes[$depend]($query, $this->model->$depend);
                } else {
                    $where = [
                        $depend => $this->model->$depend,
                    ];
                    $query->andWhere($where);
                }
            }
        }

        if ($isHas) {
//            if ($indexBy) {
            $indexBy = $this->getRelationObject()->getRelationPrimaryKey();
//            } else {
//                $indexBy = 'Ref_Key';
//            }

            $result = $query->indexBy($indexBy)->select($this->nameAttribute)->column();
            return $result;
        }
    }
}