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
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Client extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;

    const TYPE_HUB = 'hub';
    const TYPE_PROVIDER = 'provider';

    protected $table = 'oauth_clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'name',
        'type',
        'secret'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'secret'
    ];

    public function rules()
    {
        return [
            [['name', 'type'], ['required'], 'on' => 'create'],
            [['name'], ['min:3', 'max:255']],
            [['secret'], ['min:60', 'max:60']],
            [['type'], ['in:provider,hub']]
        ];
    }

    // public function setSecretAttribute($secret)
    // {
    //     if(!empty($secret) && Hash::needsRehash($secret)) {
    //         $secret = Hash::make($secret);
    //     }
    //     $this->attributes['secret'] = $secret;
    // }

    public function checkPassword($password)
    {
        return $password === $this->secret;
        return Hash::check($password, $this->secret);
    }
}
