<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;

class PasswordGrant extends \League\OAuth2\Server\Grant\PasswordGrant
{
  public $clientClass;

  public function completeFlow()
  {
    // Get the required params
    $clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
    if (is_null($clientId)) {
        throw new Exception\InvalidRequestException('client_id');
    }

    $clientSecret = $this->server->getRequest()->request->get('client_secret',
        $this->server->getRequest()->getPassword());
    if (is_null($clientSecret)) {
        throw new Exception\InvalidRequestException('client_secret');
    }
    $clientClass = $this->clientClass;
    // Validate client ID and client secret
    $client = \CHMS\Common\Models\BaseClient::where(['id' => $clientId, 'secret' => $clientSecret, 'allow_password_auth' => 1])->first();
    if (empty($client)) {
        $this->server->getEventEmitter()->emit(new Event\ClientAuthenticationFailedEvent($this->server->getRequest()));
        throw new Exception\InvalidClientException();
    }
    return parent::completeFlow();
  }
}
