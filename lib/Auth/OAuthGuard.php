<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Authorizer;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as GaurdContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\GuardHelpers;

class OAuthGuard implements GaurdContract
{
    use GuardHelpers;

    /**
     * @var string
     */
    protected $id;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param UserProvider $provider
     * @param Request      $request
     */
    public function __construct($id, UserProvider $provider, Request $request, array $config = [])
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->config = $config;
        $this->id = $id;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }
        $user = null;
        try {
            $resourceId = Authorizer::getResourceOwnerId();
            $resourceType = Authorizer::getResourceOwnerType();
        } catch (\Exception $e) {
            $resourceId = null;
            $resourceType = null;
        }
        if ($resourceType !== $this->id) {
            return null;
        }
        if (!empty($resourceId)) {
            $user = $this->getProvider()->retrieveById($resourceId);
        }
        return $this->user = $user;

    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }

    /**
     * Gets the provider
     *
     * @return UserProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
