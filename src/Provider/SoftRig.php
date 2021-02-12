<?php
namespace Psyfactory\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Psyfactory\Oauth2\Client\Provider\Exception\SoftRigIdentityProviderException;
use InvalidArgumentException;

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

  protected $baseUrl;
  protected $apiBaseUrl;

  /**
   * SoftRig oauth2 client provider constructor
   * @param array $options
   * @param array $collaborators
   */
  public function __construct(array $options = [], array $collaborators = [])
  {
    $this->assertRequiredOptions($options);

    $this->baseUrl = $options['baseUrl'];

    if (isset($options['apiBaseUrl']) && is_string($options['apiBaseUrl']) && strlen(trim($options['apiBaseUrl'])) > 0)
      $this->apiBaseUrl = $options['apiBaseUrl'];

    parent::__construct($options, $collaborators);
  }

  /**
   * Returns the base URL for authorizing a client.
   * @return string
   */
  public function getBaseAuthorizationUrl() : string
  {
    return $this->baseUrl . '/connect/authorize';
  }

  /**
   * Returns the base URL for requesting an access token.
   *
   * @param array $params
   * @return string
   */
  public function getBaseAccessTokenUrl(array $params) : string
  {
    return $this->baseUrl . '/connect/token';
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

    if (isset($jwt['AppFramework']))
      $apiBaseUrl = $jwt['AppFramework'];
    else if (!is_null($this->apiBaseUrl))
      $apiBaseUrl = $this->apiBaseUrl;
    else
      throw new \Exception(__METHOD__ . '; No ApiBaseUrl set and no AppFramework in token JWT');

    return $apiBaseUrl . 'api/biz/users?action=current-session';
  }

   /**
    * Create an access token from an array
    * @param array $tokenArray
    * @return \League\OAuth2\Client\Token\AccessToken
    */
   public function createAccessTokenFromArray(array $tokenArray) : AccessToken
   {
     return new AccessToken($tokenArray);
   }

  /**
   * Get the defaullt scopes
   * @return array
   */
  protected function getDefaultScopes() : array
  {
    return ['profile openid'];
  }

  /**
   * Returns the string that should be used to separate scopes when building
   * the URL for requesting an access token.
   *
   * @return string
   */
  protected function getScopeSeparator() : string
  {
    return ' ';
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
    $jwt = $this->decryptJwt($token);

    return new SoftRigResourceOwner($response, $jwt['companyKey']);
  }

  /**
   * Verifies that all required options have been passed.
   *
   * @param  array $options
   * @return void
   * @throws InvalidArgumentException
   */
  private function assertRequiredOptions(array $options)
  {
    $missing = array_diff_key(array_flip($this->getRequiredOptions()), $options);

    if (!empty($missing))
      throw new InvalidArgumentException(__METHOD__ . '; Required options not defined: ' . implode(', ', array_keys($missing)));
  }

  /**
   * Returns all options that are required.
   *
   * @return array
   */
  protected function getRequiredOptions()
  {
    return ['clientId', 'clientSecret', 'baseUrl'];
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

    if (!isset($jwt['companyKey']))
      throw new \Exception(__METHOD__ . '; No companyKey set in JWT');

    return $jwt;
  }
}