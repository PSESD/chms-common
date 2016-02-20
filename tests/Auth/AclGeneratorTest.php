<?php
namespace CHMSTests\Common\Helpers;

use CHMS\Common\Auth\AclGenerator;
use CHMSTests\Common\TestCase;

class AclGeneratorTest extends TestCase
{
    public function testLoad()
    {
        $this->app->configure('acl');
        $aclConfig = config('acl');
        $aclGenerator = new AclGenerator($aclConfig, []);
        $acl = $aclGenerator();
        $this->assertEquals($acl->getRole('hub_administrator')->getRoleId(), 'hub_administrator');
        $this->assertEquals($acl->getResource('r:publicRoute')->getResourceId(), 'r:publicRoute');
        $this->assertEquals($acl->getResource('m:Users')->getResourceId(), 'm:Users');
    }

    /**
    * @expectedException CHMS\Common\Exceptions\InvalidAclRuleException
    */
    public function testBadRouteSet()
    {
        $this->app->configure('acl');
        config(['acl.routeRules' => ['boom' => ['route-fake']]]);
        $aclConfig = config('acl');
        $aclGenerator = new AclGenerator($aclConfig, []);
        $acl = $aclGenerator();
    }

    /**
    * @expectedException CHMS\Common\Exceptions\InvalidAclRuleException
    */
    public function testBadGlobalRule()
    {
        $this->app->configure('acl');
        config(['acl.globalRules' => [['boom', 'resource' => 'fake']]]);
        $aclConfig = config('acl');
        $aclGenerator = new AclGenerator($aclConfig, []);
        $acl = $aclGenerator();
    }
}
