<?php
/**
 */

namespace execut\crudFields;


use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

trait BehaviorStub
{
    public function search() {
        $dp = $this->getBehavior('fields')->search();

        return $dp;
    }

    protected function attributesLabelsStub() {
        return [];
    }

    public function attributeLabels()
    {
        $result = ArrayHelper::merge(parent::attributeLabels(), $this->getBehavior('fields')->attributesLabels(), $this->attributesLabelsStub());

        return $result;
    }

    public function rules()
    {
        $rules = array_merge(parent::rules(), $this->getBehavior('fields')->rules(), $this->rulesStub());

        return $rules;
    }

    public function rulesStub() {
        return [];
    }
    
    public function getRelation($name, $throwException = true) {
        $relation = $this->_getRelationFromCache($name);

        if ($relation) {
            /**
             * @var ActiveQuery $query
             */
            $query = $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple']);
            if (!empty($relation['via'])) {
                $query->via($relation['via']);
            } else if (!empty($relation['viaTable'])) {
                 $query->viaTable($relation['viaTable'], $relation['viaLink']);
            }

            if (!empty($relation['inverseOf'])) {
                $query->inverseOf($relation['inverseOf']);
            }

            if (!empty($relation['scopes'])) {
                if (is_callable($relation['scopes'])) {
                    $scope = $relation['scopes'];
                    $r = $scope($query);
                    if ($r) {
                        $query = $r;
                    }
                }

                foreach ($relation['scopes'] as $scope) {
                    if (is_callable($scope)) {
                        $r = $scope($query);
                        if ($r) {
                            $query = $r;
                        }
                    } else {
                        $query->$scope();
                    }
                }
            }

            return $query;
        }

        return parent::getRelation($name, $throwException);
    }

    protected static $relationsCache = [];
    public function __get($name)
    {
        $relation = $this->_getRelationFromCache($name);
        if ($relation && !$this->isRelationPopulated($name)) {
            $relation = $this->getRelation($name);
            $relation = $relation->findFor($name, $this);
            $this->populateRelation($name, $relation);
        }

        return parent::__get($name);
    }

    public function getRowOptions() {
        return $this->getBehavior('fields')->getRowOptions();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _getRelationFromCache($name)
    {
        if (!array_key_exists($name, self::$relationsCache)) {
            self::$relationsCache[$name] = $relation = $this->getBehavior('fields')->getRelation($name);
        } else {
            $relation = self::$relationsCache[$name];
        }
        return $relation;
    }

    public function setScenario($scenario) {
        parent::setScenario($scenario);
        $this->getBehavior('fields')->setRelationsScenarioFromOwner();

        return $this;
    }

//    protected $_formName = null;
//
//    public function setFormName($formName) {
//        $this->_formName = $formName;
//
//        return $this;
//    }
//
//    public function formName() {
//        return $this->_formName;
//    }

//    public function attributes() {
//        $result = parent::attributes();
//        foreach ($this->getBehavior('fields')->getFields() as $field) {
//            $attributes = $field->attributes();
//            if (!empty($attributes)) {
//                $result = array_merge($result, $attributes);
//            }
//        }
//
//        return $result;
//    }
}