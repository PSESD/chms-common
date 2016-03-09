<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Canis\Laravel\Db\Models\Model as StandardModel;

class ClientEndpoint extends StandardModel
{

    protected $table = 'oauth_client_endpoints';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'redirect_uri',
        'client_id'
    ];


    public function rules()
    {
        return [
            [['redirect_uri', 'client_id'], ['required'], 'on' => 'create'],
            [['redirect_uri'], ['min:3', 'max:255']]
        ];
    }
}
