<?php
namespace Psyfactory\Oauth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psyfactory\Oauth2\Client\Provider\Exception\SoftRigIdentityProviderException;

/**
 * SoftRig oauth2 client
 *
 * @author kees
 */
class SoftRig extends AbstractProvider
{
  use BearerAuthorizationTrait {
    getAuthorizationHeaders as getTokenBearerAuthorizationHeaders;
  }

  const DOMAIN  = 'https://test-login.softrig.com';

  /**
   * Returns the base URL for authorizing a client.
   * @return string
   */
  public function getBaseAuthorizationUrl() : string
  {
    return self::DOMAIN . '/connect/authorize';
  }

  /**
   * Returns the base URL for requesting an access token.
   *
   * @param array $params
   * @return string
   */
  public function getBaseAccessTokenUrl(array $params) : string
  {
    return self::DOMAIN . '/connect/token';
  }

  /**
  * Returns the URL for requesting the resource owner's details.
  *
  * @param \League\OAuth2\Client\Token\AccessToken $token
  *
  * @return string
  */
  public function getResourceOwnerDetailsUrl(AccessToken $token) : string
  {
    $jwt = $this->decryptJwt($token);

    return $jwt['AppFramework'] . 'api/biz/users?action=current-session';
   }

  /**
   * Get the defaullt scopes
   * @return array
   */
  protected function getDefaultScopes() : array
  {
    return ['profile openid offline_access AppFramework'];
  }

  /**
   * Checks a provider response for errors.
   *
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param array|string      $data Parsed response data
   * @throws \Psyfactory\Oauth2\Client\Provider\Exception\SoftRigIdentityProviderException
   * @return void
   */
  protected function checkResponse(ResponseInterface $response, $data) : void
  {
    if ($response->getStatusCode() >= 400)
      throw SoftRigIdentityProviderException::clientException($response, $data);
    else if (isset($data['error']))
      throw SoftRigIdentityProviderException::oauthException($response, $data);
  }

  /**
   * Generate a user object from a successful user details request.
   *
   * @param array $response
   * @param AccessToken $token
   * @return \Psyfactory\Oauth2\Client\Provider\SoftRigResourceOwner
   */
  protected function createResourceOwner(array $response, AccessToken $token) : SoftRigResourceOwner
  {
    return new SoftRigResourceOwner($response);
  }

  /**
   * Get JWT array from access token
   * @param \League\OAuth2\Client\Token\AccessToken $token
   * @return array
   * @throws \Exception
   */
  private function decryptJwt(AccessToken $token) : array
  {
    $tokenValues = $token->getValues();

    if (!isset($tokenValues['id_token']))
      throw new \Exception(__METHOD__ . '; No id token set');

    $tokenParts   = explode(".", $tokenValues['id_token']);
    $jwt          = json_decode(base64_decode(strtr($tokenParts[1], "-_", "+/")), true);

    if (!isset($jwt['AppFramework']))
      throw new \Exception(__METHOD__ . '; No AppFramework set in JWT');

    return $jwt;
  }
}