<?php

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\AbstractService;
use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth1\Token\TokenInterface;

class BookAppOauthClient extends AbstractService
{
    protected $_oauthVerifier = null;

    public function __construct(
        Credentials $credentials,
        ClientInterface $httpClient = null,
        TokenStorageInterface $storage = null,
        SignatureInterface $signature = null,
        UriInterface $baseApiUri = null
    )
    {
        if (!isset($httpClient)) {
            $httpClient = new \OAuth\Common\Http\Client\StreamClient();
        }
        if (!isset($storage)) {
            $storage = new \OAuth\Common\Storage\Session();
        }
        if (!isset($signature)) {
            $signature = new \OAuth\OAuth1\Signature\Signature($credentials);
        }
        parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);
    }

    public function getRequestTokenEndpoint()
    {
        return new Uri(MAGENTO_BASE_URL . '/oauth/token/request');
    }

    public function getAuthorizationEndpoint()
    {
        throw new \OAuth\Common\Exception\Exception(
            'Magento REST API is 2-legged. Current operation is not available.'
        );
    }

    public function getAccessTokenEndpoint()
    {
        return new Uri(MAGENTO_BASE_URL . '/oauth/token/access');
    }

    protected function parseAccessTokenResponse($responseBody)
    {
        return $this->_parseToken($responseBody);
    }

    protected function parseRequestTokenResponse($responseBody)
    {
        $data = $this->_parseResponseBody($responseBody);
        if (isset($data['oauth_verifier'])) {
            $this->_oauthVerifier = $data['oauth_verifier'];
        }
        return $this->_parseToken($responseBody);
    }

    protected function _parseToken($responseBody)
    {
        $data = $this->_parseResponseBody($responseBody);
        $token = new StdOAuth1Token();
        $token->setRequestToken($data['oauth_token']);
        $token->setRequestTokenSecret($data['oauth_token_secret']);
        $token->setAccessToken($data['oauth_token']);
        $token->setAccessTokenSecret($data['oauth_token_secret']);
        $token->setEndOfLife(StdOAuth1Token::EOL_NEVER_EXPIRES);
        unset($data['oauth_token'], $data['oauth_token_secret']);
        $token->setExtraParams($data);
        return $token;
    }

    protected function _parseResponseBody($responseBody)
    {
        if (!is_string($responseBody)) {
            throw new TokenResponseException("Response body is expected to be a string.");
        }
        parse_str($responseBody, $data);
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException("Error occurred: '{$data['error']}'");
        }
        return $data;
    }

    protected function buildAuthorizationHeaderForAPIRequest(
        $method,
        UriInterface $uri,
        TokenInterface $token,
        $bodyParams = null
    )
    {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        if (isset($parameters['oauth_callback'])) {
            unset($parameters['oauth_callback']);
        }

        $parameters = array_merge($parameters, ['oauth_token' => $token->getAccessToken()]);
        $parameters = array_merge($parameters, $bodyParams);
        $parameters['oauth_signature'] = $this->signature->getSignature($uri, $parameters, $method);

        $authorizationHeader = 'OAuth ';
        $delimiter = '';

        foreach ($parameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';
            $delimiter = ', ';
        }

        return $authorizationHeader;
    }
}