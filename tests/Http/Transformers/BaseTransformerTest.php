<?php
namespace CHMSTests\Common\Http\Transformers;

use CHMSTests\Common\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use CHMS\Hub\Http\Transformers\BaseTransformer;
use CHMS\Common\Contracts\Acl as AclContract;
use CHMS\Hub\Models\User as UserModel;

class BaseTransformerTest extends TestCase
{
    use DatabaseMigrations;

    public function testHubAdminTransform()
    {
        $s = microtime(true);
        $model = $this->getUser();
        $acl = $this->app->make(AclContract::class);
        $acl->switchRoleContext(['hub_administrator']);
        $f = $this->getTransformer();
        $result = $f->transform($model);
        $this->assertArrayHasKey('last_login', $result);
        $this->assertArrayHasKey('ssn', $result);
        $this->assertArrayHasKey('birth_date', $result);
        $this->assertArrayNotHasKey('deleted_by', $result);
    }


    public function testStudentTransform()
    {
        $model = $this->getUser();
        $acl = $this->app->make(AclContract::class);
        $acl->switchRoleContext(['student']);
        $f = $this->getTransformer();
        $result = $f->transform($model);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayNotHasKey('first_name', $result);
    }

    public function testInstructorTransform()
    {
        $model = $this->getUser();
        $acl = $this->app->make(AclContract::class);
        $acl->switchRoleContext(['instructor']);
        $f = $this->getTransformer();
        $result = $f->transform($model);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayNotHasKey('last_login', $result);
        $this->assertArrayNotHasKey('ssn', $result);
    }


    public function getTransformer()
    {
        return new \CHMS\Hub\Http\Transformers\User;
    }
}