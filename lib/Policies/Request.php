<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request as RequestObject;
use CHMS\Common\Contracts\Acl as AclContract;

class Request extends BasePolicy
{
    public function access(Model $user, RequestObject $request)
    {
        if (!isset($request->route()[1]['as'])) {
            throw new \Exception("Unknown route");
        }
        $acl = app(AclContract::class);
        $routeName = $request->route()[1]['as'];
        return $acl->canAccessRoute($routeName);
    }
}
