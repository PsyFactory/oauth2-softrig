<?php
namespace Psyfactory\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

/**
 * SoftRig identity provider exception object
 *
 * @author Kees Brandenburg
 */
class SoftRigIdentityProviderException extends IdentityProviderException
{
  /**
   * Creates client exception from response.
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param array|string $data
   * @return \League\OAuth2\Client\Provider\Exception\IdentityProviderException
   */
  public static function clientException(ResponseInterface $response, $data) : IdentityProviderException
  {
    return self::fromResponse($response, ((is_array($data) && isset($data['message'])) ? $data['message'] : $response->getReasonPhrase()));
  }

  /**
   * Creates oauth exception from response.
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param array|string $data Parsed response data
   * @return \League\OAuth2\Client\Provider\Exception\IdentityProviderException
   */
  public static function oauthException(ResponseInterface $response, $data) : IdentityProviderException
  {
    return self::fromResponse($response, ((is_array($data) && isset($data['error'])) ? $data['error'] : $response->getReasonPhrase()));
  }

  /**
   * Creates identity exception from response.
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param string|null $message
   * @return \League\OAuth2\Client\Provider\Exception\IdentityProviderException
   */
  protected static function fromResponse(ResponseInterface $response, string $message = null) : IdentityProviderException
  {
    return new self($message, $response->getStatusCode(), (string) $response->getBody());
  }
}