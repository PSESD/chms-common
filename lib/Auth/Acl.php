<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Zend\Permissions\Acl\Acl as AclRules;
use CHMS\Common\Auth\Contexts\Guest as GuestContext;
use CHMS\Common\Auth\Contexts\RoleSet as RoleSetContext;
use CHMS\Common\Contracts\Acl as AclContract;
use CHMS\Common\Contracts\AclContext as AclContextContract;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;
use Cache;
use Auth;
use Laravel\Lumen\Application;

abstract class Acl implements AclContract
{
    const RESOURCE_ROUTE_PREFIX = 'r:';
    const ROUTE_PRIVILEGE = 'access';
    const RESOURCE_MODEL_PREFIX = 'm:';
    const RESOURCE_FIELD_PREFIX = 'f:';

    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $config;

    /**
     * Registered contexts
     * @var array
     */
    private $contexts = [];

    /**
     * Current context
     * @var string
     */
    private $currentContext;

    /**
     * @var GuardContract
     */
    private $guard;

    /**
     * @var AclRules
     */
    private $aclRulesCache;

    /**
     * Constructor
     * @param Application $app
     * @param array       $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }
    /**
     * Returns the Role model calss
     * @return string
     */
    abstract protected function getRoleModel();

    /**
     * @inheritdoc
     */
    public function setupRoles()
    {
        $roleModelClass = $this->getRoleModel();
        foreach ($this->config['roles'] as $roleId => $role) {
            if (!empty($role['virtual'])) {
                continue;
            }
            $roleModel = $roleModelClass::where('system_id', $roleId)->first();
            if (empty($roleModel)) {
                $roleModel = new $roleModelClass;
                $roleModel->system_id = $roleId;
                $roleModel->name = isset($role['name']) ? $role['name'] : $roleId;
                $roleModel->context = isset($role['context']) ? $role['context'] : 'system';
                if (!$roleModel->save()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check for access to a route
     *
     * @param  string $routeAlias
     * @return boolean
     */
    public function canAccessRoute($routeAlias)
    {
        return $this->isAllowed($this->routeResourceGenerator($routeAlias), static::ROUTE_PRIVILEGE);
    }


    /**
     * Check for access to a model
     *
     * @param  string $modelClass
     * @param  string|array $privilege
     * @return boolean
     */
    public function isAllowedModel($modelClass, $privilege = null)
    {
        return $this->isAllowed($this->modelResourceGenerator($modelClass), $privilege);
    }

    /**
     * Check for access to a model's field
     *
     * @param  string $modelClass
     * @param  string $fieldName
     * @param  string|array $privilege
     * @return boolean
     */
    public function isAllowedField($modelClass, $fieldName, $privilege = null)
    {
        return $this->isAllowed($this->fieldResourceGenerator($modelClass, $fieldName), $privilege);
    }

    /**
     * Check for a access to a resource
     *
     * @param  Zend\Permissions\Acl\Resource\ResourceInterface|string    $resource
     * @param  string|array                                              $privilege
     * @return boolean
     */
    public function isAllowed($resource = null, $privilege = null)
    {
        if (is_array($privilege)) {
            foreach ($privilege as $privilegeSingular) {
                if ($this->isAllowed($resource, $privilegeSingular)) {
                    return true;
                }
            }
            return false;
        }
        try {
            $result = $this->getContextualAcl()->isAllowed($this->getContextId(), $resource, $privilege);
        } catch (InvalidArgumentException $e) {
            return false;
        }
        return $result;
    }


    /**
     * Set up ACL rules with the current context
     *
     * @return AclRules
     */
    public function getContextualAcl()
    {
        $acl = $this->getRules();
        if (!$acl->hasRole($this->getContextId())) {
            $contextRoles = $this->getContext()->getRoles();
            if ($contextRoles instanceof RoleBucket) {
                $contextRoles = $contextRoles->toArray();
            }
            $acl->addRole(new Role($this->getContextId()), $contextRoles);
        }
        return $acl;
    }

    /**
     * Switch to a protected object context
     * @param  Model  $modelObject
     * @return AclContextContract
     */
    public function switchObjectContext(Model $modelObject = null)
    {
        return $this->initiateContext($modelObject);
    }

    /**
     * Switch to a explicit role (used in testing)
     *
     * @param  array|string|RoleBucket $roles
     * @return AclContextContract
     */
    public function switchRoleContext($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        if (is_array($roles)) {
            $roleBucket = new RoleBucket($this);
            foreach ($roles as $role) {
                $roleBucket->add($role);
            }
        } else {
            $roleBucket = $roles;
        }
        $context = new RoleSetContext($roleBucket);
        $this->registerContext($context);
        $this->currentContext = $context->getId();
        return $context;
    }

    /**
     * Get the current context
     * @return AclContextContract
     */
    public function getContext()
    {
        if (!isset($this->currentContext)) {
            $this->initiateContext();
        }
        return $this->contexts[$this->currentContext];
    }

    /**
     * Register a context
     * @param  AclContextContract $context
     * @return self
     */
    public function registerContext(AclContextContract $context)
    {
        $this->contexts[$context->getId()] = $context;
        return $this;
    }

    /**
     * Initiate a context
     * @param  Model $modelObject Optional protected model
     * @return AclContextContract
     */
    private function initiateContext(Model $modelObject = null)
    {
        $context = $this->getContextFromAuth($modelObject);
        $this->registerContext($context);
        $this->currentContext = $context->getId();
        //\dump($context->getRoles()->toArray());exit;
        return $context;
    }

    /**
     * Get context from auth
     * @param  Model $modelObject Optional protected model
     * @return AclContextContract
     */
    protected function getContextFromAuth(Model $modelObject = null)
    {
        return new GuestContext(null, $modelObject);
    }

    /**
     * Get the current context's ID
     * @return string
     */
    public function getContextId()
    {
        return $this->getContext()->getId();
    }

    /**
     * Used by `RoleBucket` to determine role level
     * @param  string $id
     * @return int
     */
    public function getRoleLevel($id)
    {
        if (isset($this->config['roles'][$id]['level'])) {
            return $this->config['roles'][$id]['level'];
        }
        return 99999;
    }

    /**
     * Get the ACL object; use cache when possible
     *
     * @param  boolean $useCache
     * @return AclRules
     */
    public function getRules($useCache = true)
    {
        if (!$useCache) { //  || env('APP_ENV') !== 'production'
            return $this->loadRules();
        }
        if ($this->aclRulesCache === null) {
            $hash = sha1(serialize($this->config));
            $key = md5(__CLASS__.__FUNCTION__);
            $value = Cache::get($key, false);
            if ($value === false || $value['hash'] !== $hash) {
                $value = [
                    'hash' => $hash,
                    'acl' => $this->loadRules()
                ];
                Cache::forever($key, $value);
            }
            $this->aclRulesCache = $value['acl'];
        }
        return $this->aclRulesCache;
    }

    /**
     * Generate the ACL rules
     *
     * @return AclRules
     */
    private function loadRules()
    {
        $generator = new AclGenerator($this->config, $this->getGeneratorConfig());
        return $generator();
    }

    /**
     * Configuration for ACL generator
     * @return array
     */
    private function getGeneratorConfig()
    {
        return [
            'modelResourceGenerator' => [$this, 'modelResourceGenerator'],
            'fieldResourceGenerator' => [$this, 'fieldResourceGenerator'],
            'routeResourceGenerator' => [$this, 'routeResourceGenerator'],
            'routePrivilege' => static::ROUTE_PRIVILEGE
        ];
    }

    public function modelResourceGenerator($modelClass)
    {
        return static::RESOURCE_MODEL_PREFIX . substr(md5($modelClass), 0, 8);
    }

    public function fieldResourceGenerator($modelClass, $fieldName)
    {
        return static::RESOURCE_FIELD_PREFIX . substr(md5($modelClass . $fieldName), 0, 8);
    }

    public function routeResourceGenerator($routeClass)
    {
        return static::RESOURCE_ROUTE_PREFIX . substr(md5($routeClass), 0, 8);
    }

    /**
     * Set the Auth Guard
     * @param GuardContract $guard
     */
    public function setGuard(GuardContract $guard)
    {
        $this->guard = $guard;
        return $this;
    }

    /**
     * Get the Auth Guard
     * @return GuardContract
     */
    public function getGuard()
    {
        if (isset($this->guard)) {
            return $this->guard;
        }
        return Auth::guard();
    }

}
