<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Contracts\Criteria as CriteriaContract;
use CHMS\Common\Contracts\Context;

abstract class BaseRepository
    implements BaseRepositoryContract
{

    /**
    * @var Model
    */
    protected $model;

    /**
    * Constructor
    */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function findById($id, $with = [], Context $context = null)
    {
        return $this->find([['id', '=', $id]], $with, $context);
    }

    /**
     * @inheritdoc
     */
    public function query($criteria = [], $with = [], Context $context = null)
    {
        $query = $this->model->newQuery();
        if ($criteria instanceof CriteriaContract) {
            $criteria->apply($query, $this);
        } elseif(!empty($criteria)) {
            $query->where($criteria);
        }
        if ($context !== null && ($contextFields = $context->getContextFields()) && !empty($contextFields)) {
            $query->where($contextFields);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function paginate($criteria = [], $with = [], Context $context = null)
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with, $context)->paginate(20, [$table .'.*']);
    }

    /**
     * @inheritdoc
     */
    public function find($criteria = [], $with = [], Context $context = null)
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with, $context)->first([$table.'.*']);
    }

    /**
     * @inheritdoc
     */
    public function findAll($criteria = [], $with = [], Context $context = null)
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with, $context)->get([$table.'.*']);
    }

    /**
     * @inheritdoc
     */
    public function create($input = [], Context $context = null)
    {
        $modelClass = get_class($this->model);

        if ($context !== null && ($contextFields = $context->getContextFields()) && !empty($contextFields)) {
            $input = array_merge($input, $contextFields);
        }
        return $modelClass::create($input);
    }

    /**
     * @inheritdoc
     */
    public function model()
    {
        return $this->model;
    }
}
