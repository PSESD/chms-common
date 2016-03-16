<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Contracts\InputGate as InputGateContract;
use CHMS\Common\Exceptions\ServerErrorHttpException;

class InputGate
    implements InputGateContract
{
    private $filters = [];

    private $validators = [];

    public function process(Model $model, array $input, $scope)
    {
        if (($filter = $this->getFilter(get_class($model)))) {
            $input = $filter->filter($model, $input, $scope, true);
        }

        if (($validator = $this->getValidator(get_class($model)))) {
            if (!$validator->validate($model, $input, $scope)) {
                return false;
            }
        }
        return $input;
    }

    protected function getFilter($objectClass)
    {
        if (isset($this->filters[$objectClass])) {
            $filterClass = $this->filters[$objectClass];
            return new $filterClass;
        }
        return false;
    }

    protected function getValidator($objectClass)
    {
        if (isset($this->validators[$objectClass])) {
            $validatorClass = $this->validators[$objectClass];
            return new $validatorClass;
        }
        return false;
    }

    public function filter($objectClass, $filterClass)
    {
        $this->filters[$objectClass] = $filterClass;
    }

    public function validator($objectClass, $validatorClass)
    {
        $this->validators[$objectClass] = $validatorClass;
    }
}