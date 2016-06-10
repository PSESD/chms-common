<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

use Illuminate\Database\Eloquent\Builder as Query;
use CHMS\Common\Repositories\BaseRepository;

interface Criteria
{
    /**
     * Apply set of criteria to a query
     * @param  Builder  $query
     * @param  BaseRepository $repository Repository with this model
     * @return Builder                    Eloquent model query
     */
    public function apply(Query $query, BaseRepository $repository);
}
