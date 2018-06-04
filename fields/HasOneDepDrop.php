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
        $widgetOptions = $field['widgetOptions'];
        unset($field['widgetOptions']);
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

    public function getMultipleInputField()
    {
        throw new Exception(__METHOD__ . ' is not implemented');
        $widgetOptions = $this->getSelect2WidgetOptions();
        unset($widgetOptions['pluginOptions']['ajax']);
        $data = $this->getData();

        return [
            'type' => DepDrop::class,
            'name' => $this->attribute,
            'value' => $this->value,
            'options' => [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $data,
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
                'select2Options' => ArrayHelper::merge($widgetOptions,
                    [
                    ]
                )
            ],
        ];
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
            if (!empty($this->model->$depend)) {
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