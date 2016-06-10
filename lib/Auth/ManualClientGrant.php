<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Client credentials grant class
 */
class ManualClientGrant extends AbstractGrant
{
    /**
     * Grant identifier
     *
     * @var string
     */
    protected $identifier = 'manual_client';

    /**
     * AuthServer instance
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server = null;

    /**
     * Access token expires in override
     *
     * @var int
     */
    protected $accessTokenTTL = null;

    /**
     * Complete the client credentials grant
     *
     * @return array
     *
     * @throws
     */
    public function completeFlow()
    {
        $selfClient = app('selfClient');
        // Get the required params
        if (is_null($selfClient)) {
            throw new Exception\InvalidClientException();
        }

        // Validate client ID and client secret
        $client = $this->server->getClientStorage()->get(
            $selfClient->id,
            $selfClient->secret,
            null,
            $this->getIdentifier()
        );

        if (($client instanceof ClientEntity) === false) {
            $this->server->getEventEmitter()->emit(new Event\ClientAuthenticationFailedEvent($this->server->getRequest()));
            throw new Exception\InvalidClientException();
        }

        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner('client', $client->getId());
        $session->associateClient($client);

        // Generate an access token
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId(SecureKey::generate());
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        foreach ($session->getScopes() as $scope) {
            $accessToken->associateScope($scope);
        }

        // Save everything
        $session->save();
        $accessToken->setSession($session);
        $accessToken->save();

        $oauthClient = new GenericProvider([
            'clientId'                => $selfClient->id,    // The client ID assigned to you by the provider
            'clientSecret'            => $selfClient->secret,   // The client password assigned to you by the provider
            'redirectUri'             => null,
            'urlAuthorize'            => null,
            'urlAccessToken'          => null,
            'urlResourceOwnerDetails' => null
        ]);
        $accessToken = new AccessToken(['access_token' => $accessToken->getId(), 'expires' => $accessToken->getExpireTime()]);
        return function ($method, $url, $options = []) use ($oauthClient, $accessToken) {
            return $oauthClient->getAuthenticatedRequest($method, $url, $accessToken, $options);
        };
    }
}
