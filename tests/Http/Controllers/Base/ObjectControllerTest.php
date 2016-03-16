<?php
namespace CHMSTests\Common\Http\Controllers\Base;

use CHMSTests\Hub\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

abstract class ObjectControllerTest extends TestCase
{
    use ObjectTrait;
    use DatabaseMigrations;


}