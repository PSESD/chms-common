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
    private $rules;
    private $config;
    private $acl;

    public function __construct(array $rules, array $config)
    {
        $this->rules = array_merge($this->getDefaultRules(), $rules);
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    public function __invoke()
    {
        $this->acl = new Acl();
        $this->loadRoles();
        $this->loadGlobalRules();
        $this->loadRouteRules();
        $this->loadModelRules();
        return $this->acl;
    }

    private function loadRoles()
    {
        foreach ($this->rules['roles'] as $role => $roleConfig) {
            $parents = isset($roleConfig['parents']) ? $roleConfig['parents'] : null;
            $this->acl->addRole(new Role($role), $parents);
        }
    }

    private function loadGlobalRules()
    {
        foreach ($this->rules['globalRules'] as $rule) {
            $this->enforceRule($rule);
        }
    }

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

    private function loadRouteRules()
    {
        foreach ($this->rules['routeRules'] as $routeAlias => $routeRuleSets) {
            $resourceId = $this->config['routePrefix']. $routeAlias;
            $this->acl->addResource(new Resource($resourceId));
            foreach ($routeRuleSets as $ruleSet) {
                $this->enforceRuleset($ruleSet, ['resources' => $resourceId]);
            }
        }
    }

    private function enforceRuleset($ruleSet, array $base)
    {
        if (!isset($this->rules['ruleSets'][$ruleSet])) {
            throw new InvalidAclRuleException("Invalid ACL configuration");
        }
        foreach ($this->rules['ruleSets'][$ruleSet] as $rule) {
            $this->enforceRule(array_merge($rule, $base));
        }
    }

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


    private function getDefaultRules()
    {
        return ['roles' => [], 'globalRules' => [], 'modelRules' => [], 'ruleSets' => [], 'routeRules' => []];
    }

    private function getDefaultConfig()
    {
        return ['modelPrefix' => 'm:', 'routePrefix' => 'r:'];
    }

    private function getEmptyRule()
    {
        return ['roles' => null, 'resources' => null, 'privileges' => null, 'resource' => null, 'assert' => null];
    }
}
