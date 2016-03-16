<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common;

use Laravel\Lumen\Application as BaseApplication;

class Application extends BaseApplication
{
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
