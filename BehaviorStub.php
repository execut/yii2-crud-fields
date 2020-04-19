<?php
/**
 */

namespace execut\crudFields;


use yii\base\UnknownPropertyException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;

trait BehaviorStub
{
    public function search() {
        $dp = $this->getBehavior(Behavior::KEY)->search();

        return $dp;
    }

    protected function attributesLabelsStub() {
        return [];
    }

    public function attributeLabels()
    {
        $result = ArrayHelper::merge(parent::attributeLabels(), $this->getBehavior(Behavior::KEY)->attributesLabels(), $this->attributesLabelsStub());

        return $result;
    }

    public function rules()
    {
        $rules = array_merge(parent::rules(), $this->getBehavior(Behavior::KEY)->rules(), $this->rulesStub());

        return $rules;
    }

    public function rulesStub() {
        return [];
    }

    public function getRelation($name, $throwException = true, $isWithoutBehaviorRelations = false) {
        /**
         * @var Behavior $behavior
         */
        if (!$isWithoutBehaviorRelations && ($behavior = $this->getBehavior(Behavior::KEY)) && $behavior->isHasRelation($name)) {// && $behavior->isInited()) {
            $relation = $behavior->getRelation($name);
            if ($relation) {
                return $relation;
            }
        }

        return parent::getRelation($name, $throwException);
    }

    protected static $relationsCache = [];
    public function __get($name)
    {
        if ($this->hasAttribute($name) || $name === 'scenario') {
            return parent::__get($name);
        }

        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            /**
             * @var Behavior $behavior
             */
            if (($behavior = $this->getBehavior(Behavior::KEY)) && $behavior->isHasRelation($name)) {// && $behavior->isInited()) {
                $relation = $behavior->getRelation($name);
                if ($relation && $relation instanceof ActiveQueryInterface) {
                    if (!$this->isRelationPopulated($name)) {
                        $relation = $relation->findFor($name, $this);
                        $this->populateRelation($name, $relation);
                    }
                }
            }

            return parent::__get($name);
        }
    }

    public function getRowOptions() {
        return $this->getBehavior(Behavior::KEY)->getRowOptions();
    }

    public function setScenario($scenario) {
        parent::setScenario($scenario);
        $this->getBehavior(Behavior::KEY)->setRelationsScenarioFromOwner();

        return $this;
    }
}