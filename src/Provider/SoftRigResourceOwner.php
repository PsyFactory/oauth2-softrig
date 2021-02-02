<?php
namespace Psyfactory\OAuth2\Client\Provider;

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
   * @var string companyKey The key of the company the user has access to
   */
  protected $response, $companyKey;

  /**
   * Creates new resource owner.
   *
   * @param array  $response
   */
  public function __construct(array $response, string $companyKey)
  {
    if (strlen(trim($companyKey)) === 0)
      throw new \InvalidArgumentException(__METHOD__ . '; Invalid company key');

    $this->response   = $response;
    $this->companyKey = $companyKey;
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
   * Get the company key
   * @return string
   */
  public function getCompanyKey()
  {
    return $this->companyKey;
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