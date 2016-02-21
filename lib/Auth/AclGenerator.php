<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Zend\Permissions\Acl\Acl;
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
     * @return Acl
     */
    public function __invoke()
    {
        $this->acl = new Acl();
        $this->loadRoles();
        $this->loadGlobalRules();
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
     * Load model rules
     */
    private function loadModelRules()
    {
        foreach ($this->rules['modelRules'] as $model => $modelRuleSets) {
            $resourceId = $this->config['modelPrefix'] . $model;
            $this->acl->addResource(new Resource($resourceId));
            foreach ($modelRuleSets as $ruleSet) {
                $this->enforceRuleset($ruleSet, ['resources' => $resourceId]);
            }
        }
    }


    /**
     * Load route rules
     */
    private function loadRouteRules()
    {
        foreach ($this->rules['routeRules'] as $routeAlias => $routeRuleSets) {
            $resourceId = $this->config['routePrefix']. $routeAlias;
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
            throw new InvalidAclRuleException("Invalid ACL configuration");
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
        return ['roles' => [], 'globalRules' => [], 'modelRules' => [], 'ruleSets' => [], 'routeRules' => []];
    }

    /**
     * Get default config
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        return ['modelPrefix' => 'm:', 'routePrefix' => 'r:', 'routePrivilege' => 'access'];
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
