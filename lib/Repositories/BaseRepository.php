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
    public function findById($id, $with = [])
    {
        return $this->find([['id', '=', $id]], $with);
    }

    /**
     * @inheritdoc
     */
    public function query($criteria = [], $with = [])
    {
        $query = $this->model->newQuery();
        if ($criteria instanceof CriteriaContract) {
            $criteria->apply($query, $this);
        } elseif(!empty($criteria)) {
            $query->where($criteria);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function paginate($criteria = [], $with = [])
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with)->paginate(20, [$table .'.*']);
    }

    /**
     * @inheritdoc
     */
    public function find($criteria = [], $with = [])
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with)->first([$table.'.*']);
    }

    /**
     * @inheritdoc
     */
    public function findAll($criteria = [], $with = [])
    {
        $table = $this->model->getTable();
        return $this->query($criteria, $with)->get([$table.'.*']);
    }

    /**
     * @inheritdoc
     */
    public function create($input = [])
    {
        $modelClass = get_class($this->model);
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
