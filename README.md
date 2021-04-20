<h1 align="center">Openid-Connect</h1>

## Installation
In your composer.json add the following to your "repositories" field:
```json
"openid-connect": {
            "type": "vcs",
            "url": "https://github.com/pinnacleqi/openid-connect"
        }
```
Then run 
```sh
composer require pinnacle/openid-connect
```

## Basic Usage
To obtain a redirect URL for an OAuth provider:
```php
$providerDto = new ProviderDto(
        $clientId,
        $clientSecret,
        $authorizationEndpoint,
        $tokenEndpoint,
        $userInfoEndpoint
);

$authenticate =  new Authenticate(
    $providerDto,
    $responseType, // ResponseType::CODE || ResponseType::Token
    $redirectUrl // Your callback URL
);

// Additional options such as:
// $authenticate->addScope();
// $authenticate->withState();
// $authenticate->withChallenge();
// etc.

return $authenticate->getAuthRedirectUrl();
```

To handle OAuth callbacks to get authorization tokens or user info:
```php
$providerDto = new ProviderDto(/*...*/);

$authorize = new Authorize(
    $providerDto,
    $redirectUrl, // Your original callback URL
    $authorizationCode, // Obtained from the callback query parameters
    $codeResolver // Include if PKCE was used initially
);

$authorize->getAccessToken(); // To get the access token
$authorize->getUserInfo(); // To get the userInfoDto.

// e.g. getting the user's subject identifier:
return $authorize->getUserInfo()->getSubjectIdentifier();
```
