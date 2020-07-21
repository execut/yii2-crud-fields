<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\depdrop\DepDrop;
use kartik\detail\DetailView;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\UnsetArrayValue;
use yii\helpers\Url;

/**
 * Field for DepDrop widget. This is widget for dependent fields.
 * @see DepDrop
 * @package execut\crudFields
 */
class HasOneDepDrop extends HasOneSelect2
{
    /**
     * @var array Array of depends for DepDrop widget
     * @see DepDrop
     */
    public $depends = [];
    /**
     * @var string Depended attribute for DepDrop widget
     * @see DepDrop
     */
    public $dependedAttribute = null;
    /**
     * @var array Scopes for search data from relation object
     */
    public $searchDataScopes = [];
    /**
     * @var string Name attribute from relation object. @TODO delegate to relation object?
     */
    public $nameAttribute = null;

    /**
     * {@inheritdoc}
     */
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
        $data = $this->getData();

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
                    'nameParam' => 'text',
                    'allParam' => $this->dependedAttribute,
                    'ajaxSettings' => [
                        'method' => 'get',
                    ],
                    'url' => Url::to($this->getUrl()),
                    'depends' => $this->getDepends(),
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getMultipleInputField()
    {
        throw new Exception(__METHOD__ . ' is not implemented');
    }

    /**
     * Returns from depends inputs ids
     * @return array
     */
    protected function getDepends()
    {
        $result = [];
        foreach ($this->depends as $depend) {
            $result[] = Html::getInputId($this->model, $depend);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $query = clone $this->getRelationObject()->getQuery();
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
            $indexBy = $this->getRelationObject()->getRelationPrimaryKey();
            $result = $query->indexBy($indexBy)->select($this->nameAttribute)->column();
            return $result;
        }

        return null;
    }
}
