<?php
namespace CHMSTests\Common\Helpers;

use CHMS\Common\Helpers\PdoDetector;
use CHMSTests\Common\TestCase;

class PdoDetectorTest extends TestCase
{
    private function resetEnv()
    {
        putenv('DATABASE_URL=');
        putenv('DB_CONNECTION=');
        putenv('DB_USERNAME=');
        putenv('DB_HOST=');
        putenv('DB_PASSWORD=');
        putenv('DB_PORT=');
        putenv('DB_DATABASE=');
    }

    public function testPdoDetectorTest()
    {
        $this->resetEnv();
        putenv('DATABASE_URL=mysql://test_user:test_password@test_hostname:3306/test_database');
        PdoDetector::detect();
        $this->assertEquals(env('DB_CONNECTION'), 'mysql');
        $this->assertEquals(env('DB_USERNAME'), 'test_user');
        $this->assertEquals(env('DB_HOST'), 'test_hostname');
        $this->assertEquals(env('DB_PASSWORD'), 'test_password');
        $this->assertEquals(env('DB_PORT'), '3306');
        $this->assertEquals(env('DB_DATABASE'), 'test_database');
    }
}
