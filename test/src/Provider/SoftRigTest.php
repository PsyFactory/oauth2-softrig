<?php
namespace Psyfactory\OAuth2\Client\Test\Provider;
use Psyfactory\OAuth2\Client\Provider\SoftRig;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class SoftRigTest extends TestCase
{
  public function testRequiredOptions()
  {
    // Additionally, these options are required by the GenericProvider
    $required = [
      'clientId'      => 'ID',
      'clientSecret'  => 'SECRET',
      'baseUrl'       => 'URL'
    ];

    foreach ($required as $key => $value)
    {
      // Test each of the required options by removing a single value
      // and attempting to create a new provider.
      $options = $required;
      unset($options[$key]);

      try
      {
        $provider = new SoftRig($options);
      }
      catch (\Exception $e)
      {
        $this->assertInstanceOf(InvalidArgumentException::class, $e);
      }
    }

    $provider = new SoftRig($required + []);
  }

  public function testAuthorizationUrl()
  {
    $provider = new SoftRig(['clientId' => 'ID', 'clientSecret' => 'SECRET', 'baseUrl' => 'URL', 'redirectUri' => 'URI']);

    $url = $provider->getAuthorizationUrl();
    $uri = parse_url($url);
    parse_str($uri['query'], $query);

    $this->assertArrayHasKey('client_id', $query);
    $this->assertArrayHasKey('response_type', $query);
    $this->assertArrayHasKey('redirect_uri', $query);
  }
}