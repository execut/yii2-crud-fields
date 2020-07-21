<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class QueryFromConfigFactory
 * @package execut\crudFields
 */
class QueryFromConfigFactory
{
    protected $params = [];
    protected $model = null;
    public function __construct(array $params = null, ActiveRecordInterface $model = null)
    {
        $this->params = $params;
        $this->model = $model;
    }

    /**
     * @return ActiveRecordInterface|null
     */
    public function getModel(): ?ActiveRecordInterface
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param ActiveRecordInterface $model
     */
    public function setModel(ActiveRecordInterface $model): void
    {
        $this->model = $model;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function create()
    {
        $relation = $this->params;
        /**
         * @var ActiveQueryInterface $query
         */
        $query = $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple']);
        if (!empty($relation['via'])) {
            $query->via($relation['via']);
        } elseif (!empty($relation['viaTable'])) {
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

    protected function createRelationQuery($class, $link, $multiple)
    {
        /* @var $class ActiveRecordInterface */
        /* @var $query ActiveQuery */
        $query = $class::find();
        $query->primaryModel = $this->model;
        $query->link = $link;
        $query->multiple = $multiple;
        return $query;
    }
}
