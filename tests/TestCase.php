<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMSTests\Common;

class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    protected $serverHeaders = [];
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        if (!class_exists('Laravel\Lumen\Application')) {
            require_once __DIR__.'/../vendor/autoload.php';
        }
        $app = new \Laravel\Lumen\Application(
            realpath(__DIR__)
        );
		$app->withFacades();
		$app->withEloquent();
        return $app;
    }
}
