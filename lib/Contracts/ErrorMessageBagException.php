<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

use Illuminate\Support\MessageBag;

interface ErrorMessageBagException
{
    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors();

    /**
     * Get the errors message bag.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors();
}