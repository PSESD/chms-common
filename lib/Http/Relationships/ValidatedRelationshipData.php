<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Relationships;

use Illuminate\Database\Eloquent\Model;

class ValidatedRelationshipData
{
    private $tasks = [];
    
    public function getHandleTasks()
    {
        return $this->tasks;
    }

    public function addHandleTasks($key, callable $callable)
    {
        $this->tasks[$key] = $callable;
        return true;
    }
}