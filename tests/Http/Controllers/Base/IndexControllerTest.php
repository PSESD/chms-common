<?php
namespace CHMSTests\Common\Http\Controllers\Base;

use CHMSTests\Common\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

abstract class IndexControllerTest extends TestCase
{
    use ObjectTrait;
    use DatabaseMigrations;

}