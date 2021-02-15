# SoftRig Provider for OAuth 2.0 Client
This package provides SoftRig OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

```
composer require psyfactory/oauth2-softrig
```

## Usage

```php
$provider  = new \Psyfactory\OAuth2\Client\Provider\SoftRig([
  'clientId'          => YOUR_CLIENT_ID,
  'clientSecret'      => YOUR_CLIENT_SECRET,
  'baseUrl'           => 'https://test-login.softrig.com',
  'apiBaseUrl'        => 'https://test.softrig.com',
  'redirectUri'       => YOUR_REDIRECT_URL
]);

// Get authorization code
if (!isset($_GET['code']))
{
  // Get authorization URL
  $authorizationUrl = $provider->getAuthorizationUrl(['scope' => ['profile', 'openid', 'offline_access', 'AppFramework']]);

  // Get the state generated for you and store it to the session.
  $_SESSION['oauth2state'] = $provider->getState();

  // Redirect the user to the authorization URL.
  header('Location: ' . $authorizationUrl);
  exit;
// Check for errors
}
elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state']))
{
    if (isset($_SESSION['oauth2state']))
      unset($_SESSION['oauth2state']);

    exit('Invalid state');
}
else
{
  try
  {
    // Get access token
    $accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);

    // Get resource owner
    $resourceOwner = $provider->getResourceOwner($accessToken);

    // Refresh access token
    $newAccessToken = $this->provider->getAccessToken('refresh_token', ['refresh_token' => $accessToken->getRefreshToken()]);
  }
  catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e)
  {
    exit($e->getMessage());
  }
}
```

For more information see the PHP League's general usage examples.

## License
The MIT License (MIT). Please see [License File](https://github.com/PsyFactory/oauth2-softrig/blob/master/LICENSE) for more information.