<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories;

use Illuminate\Http\Response;
use CHMS\Common\Contracts\Context;

interface BaseRepositoryContract
{
    /**
     * Find one object (by ID)
     *
     * @param  array|CriteriaSet   $criteria
     * @param  Context              $context (optional)
     * @return Model
     */
    public function findById($id, $with = [], Context $context = null);

    /**
     * Find one object
     *
     * @param  array|CriteriaSet   $criteria
     * @param  Context              $context (optional)
     * @return Model
     */
    public function find($criteria = [], $with = [], Context $context = null);


    /**
     * Query objects
     *
     * @param  array|CriteriaSet   $criteria
     * @param  Context              $context (optional)
     * @return array
     */
    public function query($criteria = [], $with = [], Context $context = null);

    /**
     * Find all objects and return paginator
     *
     * @param  array|CriteriaSet   $criteria
     * @param  Context              $context (optional)
     * @return array
     */
    public function paginate($criteria = [], $with = [], Context $context = null);

    /**
     * Find all objects
     *
     * @param  array|CriteriaSet   $criteria
     * @param  Context              $context (optional)
     * @return array
     */
    public function findAll($criteria = [], $with = [], Context $context = null);

    /**
     * Create a new object
     *
     * @param  array           $input
     * @param  Context              $context (optional)
     * @return Model|boolean
     */
    public function create($input = [], Context $context = null);


    /**
     * Return the provider's model
     *
     * @return Model
     */
    public function model();
}
