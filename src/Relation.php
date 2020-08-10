<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use execut\crudFields\fields\Field;
use execut\crudFields\relation\UrlMaker;
use yii\base\BaseObject;
use yii\base\Exception as ExceptionAlias1;
use yii\base\NotSupportedException as NotSupportedException;
use yii\base\UnknownMethodException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\db\Exception as ExceptionAlias;
use yii\db\Expression;
use yii\db\pgsql\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Class Relation
 * @package execut\crudFields
 */
class Relation extends BaseObject
{
    /**
     * @var Field Owner field
     */
    public $field = null;
    /**
     * @var ActiveRecord
     */
    public $model = null;
    /**
     * @var string Relation name attribute
     */
    public $nameAttribute = 'name';
    /**
     * @var string Relation value attribute
     */
    public $valueAttribute = null;
    /**
     * @var array Relation query with
     */
    public $with = null;
    /**
     * @var string Relation attribute for ordering list items data
     */
    public $orderByAttribute = null;
    /**
     * @var array Link for editing related models
     */
    public $updateUrl = null;
    /**
     * @var string Attribute with relation foreign key
     */
    public $attribute = null;
    /**
     * @var integer Records limit for column value
     */
    public $columnRecordsLimit = null;
    /**
     * @var array Related models list url
     */
    protected $url = null;
    /**
     * @var string Attribute for filtering by the flag of the presence of relation records
     */
    public $isHasRelationAttribute = false;
    /**
     * @var bool Display relation links or not
     */
    public $isNoRenderRelationLink = false;
    /**
     * @var string Relation value
     */
    public $value = null;
    /**
     * @var string Relation label for links
     */
    public $label = null;
    /**
     * @var string Id attribute for list items data
     */
    public $idAttribute = null;
    /**
     * @var string Via grouping @TODO WTF?
     */
    public $groupByVia = null;
    /**
     * @var string Relation name
     */
    protected $_name = null;
    /**
     * @var UrlMaker Url maker instance
     */
    protected $urlMaker = null;
    /**
     * @var LinkRenderer Link renderer instance
     */
    protected $linkRenderer = null;
    /**
     * @var ActiveQueryInterface Relation query
     */
    protected $query;

    /**
     * Sets relation name
     * @param string $name Relation name
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Calculate and returns id attribute for list items data
     * @return string|null
     * @throws ExceptionAlias
     */
    public function getIdAttribute()
    {
        if ($this->idAttribute !== null) {
            return $this->idAttribute;
        }

        $relationQuery = $this->getQuery();
        if ($relationQuery) {
            return $this->attribute;
        }

        return null;
    }

    /**
     * Returns relation name. Calculated from the attribute, if not specified directly
     * @return string|null
     * @throws ExceptionAlias
     */
    public function getName()
    {
        if ($this->_name === null) {
            $this->_name = $this->getRelationNameFromAttribute();
        }

        return $this->_name;
    }

    /**
     * Returns query with
     * @return array|string|null
     * @throws ExceptionAlias
     */
    public function getWith()
    {
        if ($this->with === null) {
            return $this->getName();
        }

        return $this->with;
    }

    /**
     * Apply query scopes for relation
     * @param ActiveQueryInterface $query
     * @return ActiveQueryInterface
     * @throws ExceptionAlias
     * @throws NotSupportedException
     */
    public function applyScopes(ActiveQueryInterface $query)
    {
        $relationQuery = $this->getQuery();
        if ($relationQuery && $relationQuery->multiple) {
            $value = $this->getValue();
            if (!empty($value)) {
                if ($this->isVia()) {
                    $pk = $this->getPrimaryKey();
                } else {
                    $pk = $this->getRelationPrimaryKey();
                }

                if (is_array($value) && current($value) instanceof ActiveRecord) {
                    $relationPk = $this->getRelationPrimaryKey();
                    $value = ArrayHelper::map($value, $relationPk, $relationPk);
                    $value = array_values($value);
                }

                if (is_int($value)) {
                    $value = [$value];
                }

                $value = array_filter($value);
                if (!empty($value)) {
                    if ($this->model->getDb()->getSchema() instanceof Schema) {
                        $attributePrefix = $this->model->tableName() . '.';
                    } else {
                        $attributePrefix = '';
                    }

                    if ($this->isVia()) {
                        $viaRelationQuery = clone $this->getViaRelationQuery();
                        $viaRelationQuery->select(key($viaRelationQuery->link));
                        $whereAttribute = current($this->getQuery()->link);
                        $viaRelationQuery->andWhere([
                            $whereAttribute => $value,
                        ]);
                        $viaRelationQuery->link = null;
                        $viaRelationQuery->primaryModel = null;
                    } else {
                        $viaRelationQuery = clone $this->getQuery();
                        $viaRelationQuery->select(key($viaRelationQuery->link));
                        $viaRelationQuery->indexBy = key($viaRelationQuery->link);
                        $whereAttribute = current($viaRelationQuery->link);
                        $viaRelationQuery->andWhere([
                            $whereAttribute => $value
                        ]);

                        $viaRelationQuery->link = null;
                        $viaRelationQuery->primaryModel = null;
                    }

                    $query->andWhere([
                        $attributePrefix . $pk => $viaRelationQuery,
                    ]);
                }
            }
        }

        if ($this->getWith() && $this->columnRecordsLimit === null) {
            $query->with($this->getWith());
        }

        $this->applyScopeIsExistRecords($query);

        return $query;
    }

    /**
     * Returns first row from relation data for current value
     * @return string
     */
    public function getSourceText()
    {
        $result = $this->getSourcesText();

        return current($result);
    }

    /**
     * Returns relation model class
     * @return string
     * @throws ExceptionAlias
     */
    public function getRelationModelClass()
    {
        $modelClass = $this->getQuery()->modelClass;

        return $modelClass;
    }

    /**
     * Returns relation form name
     * @return mixed
     */
    public function getRelationFormName()
    {
        $model = $this->getRelationModel();

        return $model->formName();
    }

    /**
     * Returns relation primary key
     * @return mixed
     * @throws ExceptionAlias
     */
    public function getRelationPrimaryKey()
    {
        $relationQuery = $this->getQuery();
        $class = $relationQuery->modelClass;
        return current($class::primaryKey());
    }


    /**
     * Returns relation data for current value
     * @return array
     * @throws ExceptionAlias
     */
    public function getSourcesText(): array
    {
        $sourceInitText = [];
        $nameAttribute = $this->nameAttribute;
        $model = $this->model;
        $modelClass = $this->getRelationModelClass();
        $value = $this->getValue();
        if (empty($value)) {
            return [];
        }

        if ($this->isManyToMany()) {
            $relationQuery = $this->getQuery();
            $via = $relationQuery->via;
            if ($via instanceof ActiveQuery) {
                /**
                 * @todo Needed autodetect via PK
                 */
                $sourceIds = $via->select('id');
            } else {
                $viaAttribute = $this->attribute;
                if (!empty($this->model->$viaAttribute)) {
                    $sourceIds = $this->model->$viaAttribute;
                    foreach ($sourceIds as $key => $sourceId) {
                        if ($sourceId instanceof ActiveRecord) {
                            $sourceIds[$key] = $sourceId->primaryKey;
                        }
                    }
                } else {
                    $viaRelationName = $via[0];
                    $viaModels = $this->model->$viaRelationName;
                    $sourceIds = [];
                    foreach ($viaModels as $viaModel) {
                        $sourceIds[$viaModel->$viaAttribute] = ArrayHelper::getValue($viaModel, $nameAttribute);
                    }
                }
            }
        } else {
            $attribute = $this->attribute;
            if (!empty($model->$attribute)) {
                $sourceIds = [];
                if (is_array($model->$attribute)) {
                    $sourceIds = $model->$attribute;
                } else {
                    $sourceIds[] = $model->$attribute;
                }

                foreach ($sourceIds as $key => $sourceId) {
                    if (is_callable($sourceId)) {
                        $sourceIds[$key] = $sourceId();
                        continue;
                    }

                    if ($sourceId instanceof ActiveRecord) {
                        $sourceInitText[$sourceId->primaryKey] = ArrayHelper::getValue($sourceId, $nameAttribute);
                    }
                }

                if (!empty($sourceInitText)) {
                    return $sourceInitText;
                }
            }
        }

        if (!empty($sourceIds)) {
            $pk = $this->getRelationPrimaryKey();
            $q = $modelClass::find()->andWhere([$pk => $sourceIds]);
            $models = $q->all();
            $sourceInitText = ArrayHelper::map($models, $pk, $nameAttribute);
        }

        return $sourceInitText;
    }

    /**
     * Returns relation data for list items
     * @param bool $asLink Is render every item as link
     * @return array
     * @throws ExceptionAlias
     */
    public function getData($asLink = false): array
    {
        $data = ['' => ''];

        $models = $this->getRelatedModels();

        $relationQuery = $this->getQuery();
        $idAttribute = key($relationQuery->link);
        if ($asLink) {
            $nameAttribute = function ($model) {
                return $this->getLink($model, $this->nameAttribute);
            };
        } else {
            $nameAttribute = $this->nameAttribute;
        }

        $data = ArrayHelper::merge($data, ArrayHelper::map($models, $idAttribute, $nameAttribute));
        return $data;
    }

    /**
     * Relation query is has via query
     * @return bool
     * @throws ExceptionAlias
     */
    public function isVia()
    {
        return $this->getQuery()->via !== null;
    }

    /**
     * Returns relation column value
     * @param $row
     * @return mixed|string|void
     * @throws ExceptionAlias
     */
    public function getColumnValue($row)
    {
        if (!$this->isHasMany()) {
            if ($this->valueAttribute !== null) {
                $attribute = $this->valueAttribute;
            } else {
                $attribute = $this->name . '.' . $this->nameAttribute;
            }

            return $this->getLink($row, $attribute);
        } else {
            if ($this->columnRecordsLimit === null) {
                $models = $row->{$this->getName()};
                $count = count($models);
            } else {
                /**
                 * @var ActiveQuery $relation
                 */
                $relation = $row->getRelation($this->getName());
                if (!empty($relation->via)) {
                    $via = $relation->via[1];
                    if ($this->groupByVia) {
                        $via->select($this->groupByVia)->groupBy($this->groupByVia);
                    }

                    if ($this->columnRecordsLimit !== null) {
                        $count = $via->count();
                    }
                } else {
                    if ($this->columnRecordsLimit !== null) {
                        $count = $relation->count();
                    }
                }

                $models = $relation->limit($this->columnRecordsLimit)->all();
            }

            $result = [];
            $nameAttribute = $this->nameAttribute;
            foreach ($models as $key => $model) {
                $result[] = $this->getLink($model, $nameAttribute);
            }

            $result = implode(', ', $result);

            if ($this->columnRecordsLimit !== null) {
                $label = ' всего ' . $count . ' ';
                $result .= $label;
                if (empty($relation->via)) {
                    $url = $this->url;
                    if ($url !== null) {
                        if (is_string($url)) {
                            $url = [$url];
                        }

                        $attribute = key($this->getQuery()->link);
                        if (empty($url[$this->getRelationFormName()])) {
                            $url[$this->getRelationFormName()] = [];
                        }

                        $url[$this->getRelationFormName()][$attribute] = $row->primaryKey;
                        $result .= ' ' . Html::a('>>>', Url::to($url));
                    }
                }
            }

            return $result;
        }
    }

    /**
     * Sets relation query
     * @param $q
     */
    public function setQuery($q)
    {
        $this->query = $q;
    }

    /**
     * Returns relation query
     * @return ActiveQueryInterface
     * @throws ExceptionAlias
     */
    public function getQuery()
    {
        if ($this->query !== null) {
            return $this->query;
        }

        if (!$this->model) {
            return null;
        }

        $name = $this->getName();
        $relationQuery = $this->model->getRelation($name, false, true);
        if (!$relationQuery) {
            $relationQuery = $this->model->getPluginsRelation($name);
        }

        if (!$relationQuery) {
            $getter = 'get' . $name;
            try {
                $relationQuery = $this->model->$getter();
            } catch (UnknownMethodException $e) {
                if (!$relationQuery) {
                    throw new ExceptionAlias('Relation ' . $name . ' is not has query!');
                }
            }
        }

        return $this->query = $relationQuery;
    }

    /**
     * Returns via relation
     * @return ActiveQueryInterface
     * @throws ExceptionAlias
     */
    public function getViaRelation()
    {
        $relationQuery = $this->getQuery();

        $via = $relationQuery->via;
        if ($via instanceof ActiveQuery) {
            return $via;
        }

        $viaRelation = $via[0];
        return $viaRelation;
    }

    /**
     * Returns relation via query
     * @return ActiveQuery|ActiveQueryInterface|null
     * @throws ExceptionAlias
     */
    public function getViaRelationQuery()
    {
        $viaRelation = $this->getViaRelation();
        $viaRelationQuery = $this->model->getRelation($viaRelation);
        return $viaRelationQuery;
    }

    /**
     * Returns via from attribute
     * @return string
     * @throws ExceptionAlias
     */
    public function getViaFromAttribute()
    {
        return key($this->getViaRelationQuery()->link);
    }

    /**
     * Returns via to attribute
     * @return string
     * @throws ExceptionAlias
     */
    public function getViaToAttribute()
    {
        return current($this->getQuery()->link);
    }

    /**
     * Returns via relation modelClass
     * @return string
     * @throws ExceptionAlias
     */
    public function getViaRelationModelClass()
    {
        return $this->getViaRelationQuery()->modelClass;
    }

    /**
     * Returns all models from relation
     * @return ActiveRecordInterface[]
     * @throws ExceptionAlias
     */
    public function getRelatedModels()
    {
        $relationQuery = clone $this->getQuery();
        $relationQuery->link = null;
        $relationQuery->primaryModel = null;

        $orderByAttribute = $this->orderByAttribute;
        if ($orderByAttribute === null) {
            $orderByAttribute = $this->nameAttribute;
        }

        if ($orderByAttribute !== null && $orderByAttribute !== false) {
            $relationQuery->orderBy($orderByAttribute);
        }

        $models = $relationQuery->all();
        return $models;
    }

    /**
     * Relation is relation a has many
     * @return mixed
     * @throws ExceptionAlias
     */
    public function isHasMany()
    {
        return $this->getQuery()->multiple;
    }

    /**
     * Returns all relation fields for sub forms
     * @return array
     * @throws ExceptionAlias
     */
    public function getRelationFields()
    {
        $model = $this->getRelationModel();
        if (!$model->getBehavior('fields') || $this->isManyToMany() || $this->isHasMany()) {
            return [];
        }

        $model->scenario = Field::SCENARIO_FORM;
        $fields = $model->getFields();
        $pks = $model->primaryKey();
        foreach ($fields as $key => $field) {
            if (!$field->isRenderInRelationForm) {
                unset($fields[$key]);
            }

            if ($field->attribute === null || in_array($key, $pks)) {
                unset($fields[$key]);
            }
        }

        /**
         * TODO copy-paste from Behavior sort logic
         */
        uasort($fields, function ($a, $b) {
            return $a->order > $b->order;
        });

        $result = [];
        foreach ($fields as $key => $field) {
            $result[$this->getName() . '_' . $key] = $field;
        }

        return $fields;
    }

    /**
     * Returns relation model instance
     * @param bool $isFirst Get the first model from an owner relationship
     * @return ActiveRecordInterface
     * @throws ExceptionAlias
     */
    public function getRelationModel($isFirst = false)
    {
        $name = $this->getName();
        if ((!$this->isHasMany() || $isFirst) && ($model = $this->model->$name)) { //$this->field->getValue() &&
            if ($isFirst) {
                if (current($model)) {
                    return current($model);
                }
            } else {
                return $model;
            }
        }

        $relationModelClass = $this->getRelationModelClass();
        $model = new $relationModelClass;

        return $model;
    }

    /**
     * Returns update url params from model
     * @param ActiveRecordInterface $row
     * @return array
     * @throws ExceptionAlias
     */
    public function getUpdateUrlParamsForModel($row)
    {
        $urlMaker = $this->getUrlMaker();

        return $urlMaker->make($row, $this->getIdAttribute());
    }

    /**
     * Returns link html from model
     * @param ActiveRecordInterface $row Target model
     * @param string $nameAttribute Name attribute for link content
     * @param string $idAttribute Id attribute for url
     * @return string
     * @throws ExceptionAlias
     * @throws ExceptionAlias1
     */
    public function getLink($row, $nameAttribute, $idAttribute = null)
    {
        if ($idAttribute === null) {
            $idAttribute = $this->getIdAttribute();
        }

        $linkRenderer = $this->configureLinkRenderer($row, $nameAttribute, $idAttribute);

        return $linkRenderer->render();
    }

    /**
     * Returns primaryKey
     * @return string
     */
    protected function getPrimaryKey()
    {
        return current($this->model::primaryKey());
    }

    /**
     * Calculate relation name from attribute name
     * @return string
     */
    protected function getRelationNameFromAttribute()
    {
        $attribute = $this->attribute;
        $relationName = lcfirst(Inflector::id2camel(str_replace('_id', '', $attribute), '_'));

        return $relationName;
    }

    /**
     * Relation is relation a many to many
     * @return bool
     * @throws ExceptionAlias
     */
    protected function isManyToMany()
    {
        $relationQuery = $this->getQuery();

        return $relationQuery->multiple && $this->isVia();
    }

    /**
     * Filtering by the flag of the presence of relation records
     * @param ActiveQueryInterface $query Query for filtering
     * @throws ExceptionAlias
     */
    public function applyScopeIsExistRecords(ActiveQueryInterface $query)
    {
        $attribute = $this->isHasRelationAttribute;
        if (!$attribute) {
            return;
        }

        if ($this->isManyToMany()) {
            $relatedModels = $this->getRelatedModels();
            foreach ($relatedModels as $model) {
                $relationQuery = $this->getQuery();
                $relationQuery->primaryModel = null;
                if ($rowModel->$attribute == '1') {
                    $operator = 'IN';
                    $relationQuery->select(key($relationQuery->link));
                    $query->andWhere([
                        $operator,
                        current($relationQuery->link),
                        $relationQuery,
                    ]);
                } else {
                    $relationQuery->andWhere([
                        $relatedModel->tableName() . '.' . key($relationQuery->link) => new Expression($this->model->tableName() . '.' . current($relationQuery->link)),
                    ])->select(new Expression('1'));
                    $query->andWhere([
                        'NOT EXISTS',
                        $relationQuery
                    ]);
                }
            }
        } elseif ($this->isHasMany()) {
            $model = $this->getRelationModel(true);
            $value = $this->model->$attribute;
            $relationQuery = $this->getQuery();
            $relationQuery->primaryModel = null;
            if ($value == '1') {
                $operator = 'IN';
                $relationQuery->select(key($relationQuery->link));
                $query->andWhere([
                    $operator,
                    current($relationQuery->link),
                    $relationQuery,
                ]);
            } elseif ($value == '0') {
                $relationQuery->andWhere([
                    $model->tableName() . '.' . key($relationQuery->link) => new Expression($this->model->tableName() . '.' . current($relationQuery->link)),
                ])->select(new Expression('1'));
                $query->andWhere([
                    'NOT EXISTS',
                    $relationQuery
                ]);
            }
        } else {
            $model = $this->model;
            $value = $model->$attribute;
            $whereAttribute = $model->tableName() . '.' . $this->attribute;
            if ($value === '1') {
                $query->andWhere([
                    'NOT',
                    [
                        $whereAttribute => null,
                    ]
                ]);
            } elseif ($value === '0') {
                $query->andWhere([
                    $whereAttribute => null,
                ]);
            }
        }
    }

    /**
     * Returns current value
     * @return string
     * @throws ExceptionAlias1
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if ($this->field === null) {
            return null;
        }

        $value = $this->field->getValue();

        return $value;
    }

    /**
     * Sets UrlMaker instance
     * @param UrlMaker $urlMaker
     */
    public function setUrlMaker(UrlMaker $urlMaker): void
    {
        $this->urlMaker = $urlMaker;
    }

    /**
     * Returns UrlMaker instance
     * @return UrlMaker
     */
    public function getUrlMaker()
    {
        if ($this->urlMaker !== null) {
            $urlMaker = $this->urlMaker;
        } else {
            $urlMaker = new UrlMaker($this->url, $this->updateUrl, $this->isNoRenderRelationLink);
        }
        return $urlMaker;
    }

    /**
     * Configure link renderer
     * @param $row
     * @param $nameAttribute
     * @return LinkRenderer
     * @throws ExceptionAlias
     */
    public function configureLinkRenderer($row, $nameAttribute): LinkRenderer
    {
        $url = $this->getUpdateUrlParamsForModel($row);
        $linkRenderer = $this->getLinkRenderer();
        $linkRenderer->setModel($row)
            ->setNameAttribute($nameAttribute)
            ->setIdAttribute($this->getIdAttribute())
            ->setUrl($url);

        return $linkRenderer;
    }

    /**
     * Sets links renderer instance
     * @param LinkRenderer $linkRenderer
     */
    public function setLinkRenderer(LinkRenderer $linkRenderer): void
    {
        $this->linkRenderer = $linkRenderer;
    }

    /**
     * Gets an instance of the link renderer. Created if it doesn't exist
     * @return LinkRenderer
     */
    public function getLinkRenderer()
    {
        if ($this->linkRenderer === null) {
            $this->linkRenderer = new LinkRenderer(null, null, null, $this->field->getLabel());
        }

        return $this->linkRenderer;
    }

    /**
     * Sets url
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns url
     * @return array
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns column records limit
     * @return int|null
     */
    public function getColumnRecordsLimit()
    {
        return $this->columnRecordsLimit;
    }
}
