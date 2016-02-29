<?php
namespace CHMS\Common\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException as BaseUnprocessableEntityHttpException;

class UnprocessableEntityHttpException extends BaseUnprocessableEntityHttpException
{
    private $errors;

    public function __construct($message = null, $errors = null, \Exception $previous = null, $code = 0)
    {
        $this->errors = $errors;
        return parent::__construct($message, $previous, $code);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
