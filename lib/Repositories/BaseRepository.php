<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Repositories\Criteria\BaseCriteria as Criteria;

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
        $query->where($criteria);
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function paginate($criteria = [], $with = [])
    {
        return $this->query($criteria, $with)->paginate(20);
    }

    /**
     * @inheritdoc
     */
    public function find($criteria = [], $with = [])
    {
        return $this->query($criteria, $with)->first();
    }

    /**
     * @inheritdoc
     */
    public function findAll($criteria = [], $with = [])
    {
        return $this->query($criteria, $with)->get();
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
