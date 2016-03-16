<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Input\Validators;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Contracts\Acl as AclContract;
use CHMS\Common\Contracts\Validator as ValidatorContract;
use CHMS\Common\Exceptions\UnprocessableEntityHttpException;

abstract class BaseValidator
    implements ValidatorContract
{
    private $fieldAclCache = [];
    public $errors = [];

    protected function additionalValidation(Model $model, array $input, $scenario)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    final public function validate(Model $model, array $input, $scenario, $throwException = true)
    {
        $errors = [];
        $modelValidation = $this->validateModelInput($model, $input, $scenario);
        if ($modelValidation !== true) {
            $errors = array_merge($errors, $modelValidation);
        }
        $this->errors = $errors;
        $this->additionalValidation($model, $input, $scenario);
        if (!empty($this->errors)) {
            if (!$throwException) {
                return false;
            }
            $this->throwValidationException($this->errors);
        }
        return true;
    }

    private function throwValidationException($errors)
    {
        throw new UnprocessableEntityHttpException("Unable to validate input", $errors);
    }

    private function validateModelInput(Model $model, array $input, $scenario)
    {
        $validator = $this->getValidationFactory()->make($input, $model->getRules($scenario), $model->getRuleMessages($scenario));
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
        return true;
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app('validator');
    }
}
