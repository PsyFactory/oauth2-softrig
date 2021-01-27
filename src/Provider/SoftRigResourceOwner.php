<?php
namespace Psyfactory\Oauth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

/**
 * SoftRig oauth resource owner
 *
 * @author Kees Brandenburg
 */
class SoftRigResourceOwner implements ResourceOwnerInterface
{
  use ArrayAccessorTrait;

  /**
   * @var array $response		Raw response
   */
  protected $response;

  /**
   * Creates new resource owner.
   *
   * @param array  $response
   */
  public function __construct(array $response = array())
  {
    $this->response = $response;
  }

  /**
   * Get resource owner id
   *
   * @return string|null
   */
  public function getId()
  {
    return $this->getValueByKey($this->response, 'ID');
  }

  /**
   * Get resource owner email
   *
   * @return string|null
   */
  public function getEmail()
  {
    return $this->getValueByKey($this->response, 'Email');
  }

  /**
   * Get resource owner name
   *
   * @return string|null
   */
  public function getName()
  {
    return $this->getValueByKey($this->response, 'DisplayName');
  }

  /**
   * Get resource owner username
   *
   * @return string|null
   */
  public function getUsername()
  {
    return $this->getValueByKey($this->response, 'UserName');
  }

  /**
   * Return all of the owner details available as an array.
   *
   * @return array
   */
  public function toArray()
  {
    return $this->response;
  }
}