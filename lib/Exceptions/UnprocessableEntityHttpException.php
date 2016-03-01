<?php
namespace CHMS\Common\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException as BaseUnprocessableEntityHttpException;
use Illuminate\Support\MessageBag;
use CHMS\Common\Contracts\ErrorMessageBagException;

class UnprocessableEntityHttpException 
    extends BaseUnprocessableEntityHttpException
    implements ErrorMessageBagException
{
    private $errors;

    public function __construct($message = null, $errors = null, \Exception $previous = null, $code = 0)
    {
        if (is_null($errors)) {
            $this->errors = new MessageBag;
        } else {
            $this->errors = is_array($errors) ? new MessageBag($errors) : $errors;
        }

        return parent::__construct($message, $previous, $code);
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function hasErrors()
    {
        return ! $this->errors->isEmpty();
    }
}
