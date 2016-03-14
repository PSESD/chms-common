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
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class OAuthGuard implements GaurdContract, UniversalGuard
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
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  boolean $login
     * @return bool|Token
     */
    public function attempt(array $credentials = [], $login = true)
    {
        $user = $this->getProvider()->retrieveByCredentials($credentials);
        if ($user instanceof Authenticatable && $this->hasValidCredentials($user, $credentials)) {
            if ($login === true) {
                $this->login($user);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  Authenticatable|null  $user
     * @param  array  $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && $this->getProvider()->validateCredentials($user, $credentials);
    }


    /**
     * Log a user into the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function login(Authenticatable $user)
    {
        $this->setUser($user);
        return true;
    }

    /**
     * Clear user
     * @return boolean
     */
    public function logout()
    {
        $this->user = null;
        return true;
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

    /**
     * @inheritdoc
     */
    public function universalUserLogin(AuthFactory $auth)
    {
        try {
            $guard = Authorizer::getResourceOwnerType();
        } catch (\Exception $e) {
            $guard = null;
        }
        if ($guard === null) {
            return false;
        }
        $user = $auth->guard($guard)->user();
        if ($user === null) {
            $guard = false;
        }
        return $guard;
    }
}
