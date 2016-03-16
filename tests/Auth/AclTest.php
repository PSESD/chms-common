<?php
namespace CHMSTests\Common\Auth;

use Auth;
use Laravel\Lumen\Testing\DatabaseTransactions;
use CHMSTests\Common\TestCase;
use CHMS\Common\Auth\Acl;
use Laravel\Lumen\Testing\DatabaseMigrations;
use CHMS\Common\Auth\RoleBucket;
use CHMS\Common\Auth\Contexts\Guest as GuestContext;
use CHMS\Common\Auth\Contexts\Client as ClientContext;
use CHMS\Common\Auth\Contexts\RoleSet as RoleSetContext;

class AclTest extends TestCase
{
    use DatabaseMigrations;

    protected function getAcl($config = null)
    {
        $this->app->configure('acl');
        if (!isset($config)) {
            $config = config('acl');
        }
        $acl = new Acl($this->app, $config);
        return $acl;
    }

    public function testRoutes()
    {
        $n = 1000;
        $acl = $this->getAcl();
        $acl->getRules(false);
    }

    public function testGetRulesProduct()
    {
        $acl = $this->getAcl();
        $originalEnv = env('APP_ENV');
        putenv('APP_ENV=production');
        $a1 = $acl->getRules(false);
        $a2 = $acl->getRules(true);
        $a3 = $acl->getRules(true);
        $this->assertEquals($a1, $a2);
        $this->assertEquals($a2, $a3);
        putenv('APP_ENV=' . $originalEnv);
    }

    public function testRoleContextBucket()
    {
        $acl = $this->getAcl();
        $rb = new RoleBucket($acl);
        $rb->add('guest');
        $rb->add('super_administrator');
        $acl->switchRoleContext($rb);
        $context = $acl->getContext();
        $this->assertEquals($context->getRoles()->toArray(), ['guest', 'super_administrator']);
    }

    public function testRoleContextArray()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['super_administrator', 'guest']);
        $context = $acl->getContext();
        $this->assertEquals($context->getRoles()->toArray(), ['guest', 'super_administrator']);
    }

    public function testRoleContextString()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext('super_administrator');
        $context = $acl->getContext();
        $this->assertEquals($context->getRoles()->toArray(), ['super_administrator']);
    }

    public function testAccessRouteFail()
    {
        $acl = $this->getAcl();
        $this->assertFalse($acl->canAccessRoute('getUsers'));
    }

    public function testAccessRouteSuccess()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['super_administrator']);
        $this->assertTrue($acl->canAccessRoute('getUsers'));
    }


    public function testAllowedModel()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['super_administrator']);
        $this->assertTrue($acl->isAllowedModel(\CHMS\Hub\Models\User::class, 'read'));
        $acl->switchRoleContext(['student']);
        $this->assertFalse($acl->isAllowedModel(\CHMS\Hub\Models\User::class, 'set'));
    }

    public function testAllowedField()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['hub_administrator']);
        $this->assertTrue($acl->isAllowedField(\CHMS\Hub\Models\User::class, 'id', 'read'));
        $this->assertFalse($acl->isAllowedField(\CHMS\Hub\Models\User::class, 'deleted_by', 'set'));
    }

    public function testAllowedFieldSet()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['hub_administrator']);
        $this->assertTrue($acl->isAllowedField(\CHMS\Hub\Models\User::class, 'password', ['set', 'set_on_create']));
        $this->assertFalse($acl->isAllowedField(\CHMS\Hub\Models\User::class, 'deleted_by', ['set', 'set_on_create']));
    }

    public function testWeirdRoles()
    {
        $acl = $this->getAcl();
        $acl->switchRoleContext(['weird', 'super_administrator', 'guest']);
        $context = $acl->getContext();
        $this->assertEquals($context->getRoles()->toArray(), ['weird', 'guest', 'super_administrator']);
    }
}
