<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Hash;
use Illuminate\Auth\Authenticatable;
use CHMS\Common\Contracts\Selfable as SelfableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

abstract class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    SelfableContract
{
    use Authenticatable;

    /**
     * Returns the role class
     * 
     * @return string
     */
    abstract public function getRoleClass();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'title',
        'password',
        'ssn',
        'instructor_qualifications',
        'birth_date',
        'employee_id'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'password',
        'auth_key',
        'remember_token'
    ];

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'password'], ['required'], 'on' => 'create'],
            [['first_name', 'last_name'], ['min:1']],
            [['password'], ['min:6']],
            [['email'], ['email', 'unique:users']],
            [['birth_date'], ['date', 'before:now']],
            [['ssn'], ['digits:4']],
            [['title', 'first_name', 'last_name', 'email', 'password', 'instructor_qualifications'], ['string']],
            [['first_name', 'last_name', ], ['max:100']],
            [['employee_id'], ['max:40']],
            [['email', 'title'], ['max:255']],
            [['active'], ['boolean']]
        ];
    }

    public function setPasswordAttribute($password)
    {
        if(!empty($password) && Hash::needsRehash($password)) {
            $password = Hash::make($password);
        }
        $this->attributes['password'] = $password;
    }

    public function checkPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public function roles()
    {
        return $this->belongsToMany($this->getRoleClass(), 'role_users');
    }

    public function hasRole($roleSystemId, $baseQuery = [])
    {
        $roleIds = Role::where('system_id', $roleSystemId)->pluck('id');
        if (empty($roleIds)) { return false; }
        $baseQuery['role_id'] = $roleIds;
        return $this->roles()->where($baseQuery)->count() > 0;
    }

    public function isSelfOwned(AuthorizableContract $user)
    {
        return $user->id === $this->id;
    }
}
