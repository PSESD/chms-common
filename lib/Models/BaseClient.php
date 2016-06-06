<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

class BaseClient extends Client
{
  public function getClientEndpointModel()
  {
    return ClientEndpoint::class;
  }

  public function can($ability, $arguments = [])
  {
    return false;
  }
}
