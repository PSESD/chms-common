<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

use CHMS\Common\Exceptions\InvalidAclRuleException;

class AclGenerator
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Acl
     */
    private $acl;

    /**
     * @var array
     */
    private $globalFieldRules = [];

    /**
     * Constructor
     * @param array   $rules
     * @param array   $config
     */
    public function __construct(array $rules, array $config = [])
    {
        $this->rules = array_merge($this->getDefaultRules(), $rules);
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Generate ACL object
     * @return ZendAcl
     */
    public function __invoke()
    {
        $this->acl = new ZendAcl();
        $this->loadRoles();
        $this->loadGlobalRules();
        $this->loadGlobalFieldRules();
        $this->loadRouteRules();
        $this->loadModelRules();
        return $this->acl;
    }

    /**
     * Load roles
     */
    private function loadRoles()
    {
        foreach ($this->rules['roles'] as $role => $roleConfig) {
            $parents = isset($roleConfig['parents']) ? $roleConfig['parents'] : null;
            $this->acl->addRole(new Role($role), $parents);
            $this->enforceRule(['allow', 'privileges' => 'is-' . $role, 'roles' => $role]);
        }
        if (isset($this->rules['roleSets'])) {
            foreach ($this->rules['roleSets'] as $generalRole => $roles) {
                $this->enforceRule(['allow', 'privileges' => 'is-' . $generalRole, 'roles' => $roles]);
            }
        }
    }

    /**
     * Load global rules
     */
    private function loadGlobalRules()
    {
        foreach ($this->rules['globalRules'] as $rule) {
            $this->enforceRule($rule);
        }
    }
    /**
     * Load global field rules
     */
    private function loadGlobalFieldRules()
    {
        $this->globalFieldRules = $this->rules['globalFieldRules'];
    }

    private function detectModelFieldPolicies($modelClass, $fieldAccessPolicy, $defaultFieldPolicy = [])
    {
        $model = new $modelClass;
        $acrossModelFieldPolicy = null;
        if (isset($fieldAccessPolicy['*'])) {
            $acrossModelFieldPolicy = $fieldAccessPolicy['*'];
            unset($fieldAccessPolicy['*']);
        }
        $definedPolicies = array_merge($this->globalFieldRules, $fieldAccessPolicy);
        $fields = $model->getFields();
        foreach ($model->metaFields() as $metaField) {
            $fields[] = 'meta.' . $metaField;
        }
        $policies = [];
        foreach ($fields as $field) {
            $fieldPolicies = [];
            if (isset($definedPolicies[$field])) {
                $fieldPolicies = $definedPolicies[$field];
            } elseif ($acrossModelFieldPolicy !== null) {
                $fieldPolicies = $acrossModelFieldPolicy;
            }
            if ($fieldPolicies === true) {
                $fieldPolicies = $defaultFieldPolicy;
            }
            $policies[$field] = $fieldPolicies;
        }
        return $policies;
    }

    private function loadModelFieldPolicy($modelClass, $modelPolicy) {
        $defaultFieldPolicy = isset($modelPolicy['access']) ? $modelPolicy['access'] : [];
        $fieldAccessPolicy = [];
        if (isset($modelPolicy['fields'])) {
            $fieldAccessPolicy = $modelPolicy['fields'];
        }
        $policies = $this->detectModelFieldPolicies($modelClass, $fieldAccessPolicy, $defaultFieldPolicy);
        foreach ($policies as $field => $fieldPolicies) {
            $resourceId = call_user_func_array($this->config['fieldResourceGenerator'], [$modelClass, $field]);
            $this->acl->addResource(new Resource($resourceId));
            foreach ($fieldPolicies as $ruleSet => $privileges) {
                foreach ($privileges as $privilege) {
                    $this->enforceRuleset($ruleSet, ['privileges' => $privilege, 'resources' => $resourceId]);
                }
            }
        }
    }

    /**
     * Load model rules
     */
    private function loadModelRules()
    {
        foreach ($this->rules['modelRules'] as $modelClass => $modelPolicy) {
            $resourceId = call_user_func_array($this->config['modelResourceGenerator'], [$modelClass]);
            $this->acl->addResource(new Resource($resourceId));
            $modelAccessPolicy = [];
            if (isset($modelPolicy['access'])) {
                $modelAccessPolicy = $modelPolicy['access'];
            }
            foreach ($modelAccessPolicy as $ruleSet => $privileges) {
                foreach ($privileges as $privilege) {
                    $this->enforceRuleset($ruleSet, ['privileges' => $privilege, 'resources' => $resourceId]);
                }
            }
            $this->loadModelFieldPolicy($modelClass, $modelPolicy);
        }
    }

    private function translateModelPolicyToField(array $policy)
    {
        return $policy;
    }

    /**
     * Load route rules
     */
    private function loadRouteRules()
    {
        foreach ($this->rules['routeRules'] as $routeAlias => $routeRuleSets) {
            $resourceId = call_user_func_array($this->config['routeResourceGenerator'], [$routeAlias]);
            $this->acl->addResource(new Resource($resourceId));
            foreach ($routeRuleSets as $ruleSet) {
                $this->enforceRuleset($ruleSet, ['resources' => $resourceId, 'privileges' => $this->config['routePrivilege']]);
            }
        }
    }


    /**
     * Enforce ruleset
     *
     * @param   string  $ruleSet
     * @param   array   $base
     */
    private function enforceRuleset($ruleSet, array $base)
    {
        if (!isset($this->rules['ruleSets'][$ruleSet])) {
            throw new InvalidAclRuleException("Invalid ACL configuration $ruleSet");
        }
        foreach ($this->rules['ruleSets'][$ruleSet] as $rule) {
            $this->enforceRule(array_merge($rule, $base));
        }
    }

    /**
     * Enforce rule
     *
     * @param array $rule
     */
    private function enforceRule(array $rule)
    {
        if (
            !isset($rule[0])
            || !in_array($rule[0], ['allow', 'deny'])
        ) {
            throw new InvalidAclRuleException("Invalid ACL rule");
        }
        $rule = array_merge($this->getEmptyRule(), $rule);
        $ruleType = $rule[0];
        $this->acl->{$ruleType}($rule['roles'], $rule['resources'], $rule['privileges'], $rule['assert']);
    }

    /**
     * Get default rule
     *
     * @return array
     */
    private function getDefaultRules()
    {
        return ['roles' => [], 'globalRules' => [], 'globalFieldRules' => [], 'modelRules' => [], 'ruleSets' => [], 'routeRules' => []];
    }

    /**
     * Get default config
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'modelResourceGenerator' => function($model) {
                return $model;
            }, 
            'fieldResourceGenerator' => function($model, $field) {
                return $model.$field;
            }, 
            'routeResourceGenerator' => function($route) {
                return $route;
            }, 'routePrivilege' => 'access'];
    }

    /**
     * Get empty rule
     *
     * @return array
     */
    private function getEmptyRule()
    {
        return ['roles' => null, 'resources' => null, 'privileges' => null, 'resource' => null, 'assert' => null];
    }
}
