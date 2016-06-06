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
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

abstract class Client extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable;

    const TYPE_HUB = 'hub';
    const TYPE_PROVIDER = 'provider';

    protected $table = 'oauth_clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sponsor_id',
        'name',
        'type',
        'secret',
        'allow_password_auth'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'secret'
    ];

    /**
     * Get the client endpoint model class
     * 
     * @return string
     */
    abstract public function getClientEndpointModel();

    public function rules()
    {
        return [
            [['name', 'type'], ['required'], 'on' => 'create'],
            [['name'], ['min:3', 'max:255']],
            [['secret'], ['min:60', 'max:60']],
            [['type'], ['in:provider,hub']]
        ];
    }

    public function checkPassword($password)
    {
        return $password === $this->secret;
    }

    public function endpoints()
    {
        return $this->hasMany($this->getClientEndpointModel());
    }
}
