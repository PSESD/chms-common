<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories;

use Illuminate\Http\Response;

interface BaseRepositoryContract
{
    /**
     * Find one object (by ID)
     *
     * @param  array|CriteriaSet   $criteria
     * @return Model
     */
    public function findById($id, $with = []);

    /**
     * Find one object
     *
     * @param  array|CriteriaSet   $criteria
     * @return Model
     */
    public function find($criteria = [], $with = []);


    /**
     * Query objects
     *
     * @param  array|CriteriaSet   $criteria
     * @return array
     */
    public function query($criteria = []);

    /**
     * Find all objects and return paginator
     *
     * @param  array|CriteriaSet   $criteria
     * @return array
     */
    public function paginate($criteria = [], $with = []);

    /**
     * Find all objects
     *
     * @param  array|CriteriaSet   $criteria
     * @return array
     */
    public function findAll($criteria = [], $with = []);

    /**
     * Create a new object
     *
     * @param  array           $input
     * @return Model|boolean
     */
    public function create($input = []);


    /**
     * Return the provider's model
     *
     * @return Model
     */
    public function model();
}
