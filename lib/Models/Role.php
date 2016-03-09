<?php
/**
 * Clock Hour Management System - Common
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;
use Cache;

abstract class Role extends BaseModel
{
    /**
     * Returns the user class
     * 
     * @return string
     */
    abstract public function getUserClass();

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'name',
        'system_id',
        'context'
    ];
    
    public function users()
    {
        return $this->hasMany($this->getUserClass());
    }

    public function rules()
    {
        return [
            [['name', 'system_id', 'context'], ['required'], 'on' => 'create'],
            [['name'], ['max:100']],
            [['system_id'], ['max:50']],
            [['context'], ['max:20']]
        ];
    }
}
