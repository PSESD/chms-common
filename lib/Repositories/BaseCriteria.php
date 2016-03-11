<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Repositories\Contracts\BaseRepository;

abstract class BaseCriteria
{
    /**
     * Apply set of criteria to the model
     * @param  Model|Builder  $model      Eloquent model
     * @param  BaseRepository $repository Repository with this model
     * @return Builder                    Eloquent model query
     */
    public abstract function apply($model, BaseRepository $repository);
}
