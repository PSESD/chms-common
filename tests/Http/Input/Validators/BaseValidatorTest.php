<?php
namespace CHMSTests\Common\Http\Input\Validators;

use CHMSTests\Common\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use CHMS\Hub\Models\User as UserModel;

class BaseValidatorTest extends TestCase
{
    use DatabaseMigrations;
    public function testSimpleValidate()
    {
        $model = new UserModel();
        $faker = \Faker\Factory::create();
        $input = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'password' => $faker->password
        ];
        $validator = $this->getValidator();
        $this->assertTrue($validator->validate($model, $input, 'create'));
    }

    public function testSimpleValidateFail()
    {
        $model = new UserModel();
        $faker = \Faker\Factory::create();
        $input = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email .'@coolguy.com',
            'password' => $faker->password
        ];
        $validator = $this->getValidator();
        $this->assertFalse($validator->validate($model, $input, 'create', false));
        $this->assertArrayHasKey('email', $validator->errors);
    }

    /**
     * @expectedException CHMS\Common\Exceptions\UnprocessableEntityHttpException
     */
    public function testSimpleValidateFailException()
    {
        $model = new UserModel();
        $faker = \Faker\Factory::create();
        $input = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email .'@coolguy.com',
            'password' => $faker->password
        ];
        $validator = $this->getValidator();
        $this->assertFalse($validator->validate($model, $input, 'create'));
        $this->assertArrayHasKey('email', $validator->errors);
    }

    public function getValidator()
    {
        $v = \Mockery::mock(\CHMS\Hub\Http\Input\Validators\BaseValidator::class);
        return $v;
    }
}