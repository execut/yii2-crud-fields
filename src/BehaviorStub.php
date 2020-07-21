<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use yii\base\Exception;
use yii\base\UnknownPropertyException;
use yii\data\DataProviderInterface;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Trait for CRUD-ed model
 * @package execut\crudFields
 */
trait BehaviorStub
{
    /**
     * Search proxy
     * @return DataProviderInterface
     * @see Behavior::search()
     */
    public function search()
    {
        $dp = $this->getBehavior(Behavior::KEY)->search();

        return $dp;
    }

    /**
     * Stub of attributes labels. Define here own attributes directly from model
     * @return array
     */
    protected function attributesLabelsStub()
    {
        return [];
    }

    /**
     * AttributeLabels proxy
     * @return array
     * @see Behavior::attributesLabels()
     */
    public function attributeLabels()
    {
        $result = ArrayHelper::merge(parent::attributeLabels(), $this->getBehavior(Behavior::KEY)->attributesLabels(), $this->attributesLabelsStub());

        return $result;
    }

    /**
     * Rules proxy
     * @return array
     * @see Behavior::rules()
     */
    public function rules()
    {
        $rules = array_merge(parent::rules(), $this->getBehavior(Behavior::KEY)->rules(), $this->rulesStub());

        return $rules;
    }

    /**
     * Stub of validation rules. Define here own validation rules directly from model
     * @return array
     */
    public function rulesStub()
    {
        return [];
    }

    /**
     * Returns relation by name. The search order is as follows: first relations of  CRUD fields behavior,
     * next - model relations.
     * @param string $name Relation name
     * @param bool $throwException is throw exception
     * @param bool $isWithoutBehaviorRelations Is find relation without CRUD fields behavior relation
     * @return ActiveQueryInterface|null
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function getRelation($name, $throwException = true, $isWithoutBehaviorRelations = false)
    {
        /**
         * @var Behavior $behavior
         */
        if (!$isWithoutBehaviorRelations && ($behavior = $this->getBehavior(Behavior::KEY)) && $behavior->isHasRelation($name)) {
            $relation = $behavior->getRelation($name);
            if ($relation) {
                return $relation;
            }
        }

        return parent::getRelation($name, $throwException);
    }

    /**
     * A magical method for getting relations from behavior
     * @param $name
     * @return mixed|null
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function __get($name)
    {
        $methodExists = method_exists($this, 'hasAttribute');
        if (($methodExists && $this->hasAttribute($name) || !$methodExists && property_exists($this, $name)) || $name === 'scenario') {
            return parent::__get($name);
        }

        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            /**
             * @var Behavior $behavior
             */
            if (($behavior = $this->getBehavior(Behavior::KEY)) && $behavior->isHasRelation($name)) {
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


    /**
     * Returns rowOptions for DynaGrid
     * @see DynaGrid
     * @return array
     * @throws Exception
     */
    public function getRowOptions()
    {
        return $this->getBehavior(Behavior::KEY)->getRowOptions();
    }

    /**
     * Set scenario for relations models
     * @param $scenario
     * @return $this
     */
    public function setScenario($scenario)
    {
        parent::setScenario($scenario);
        $this->getBehavior(Behavior::KEY)->setRelationsScenarioFromOwner();

        return $this;
    }
}
