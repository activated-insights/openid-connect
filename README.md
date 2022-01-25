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
$provider = new Provider(
        $identifier, // Optional parameter used to identify the provider within the application.
        $clientId,
        $clientSecret,
        $authorizationEndpoint,
        $tokenEndpoint,
        $userInfoEndpoint
);

$authenticator =  new Authenticator(
    $statePersistor, // Class should implement StatePersisterInterface.
    $logger // Class should implement the LoggerInterface.
);

$authenticationRedirectUri = $authenticator
    ->beginAuthentication($provider, $redirectUrl)
    ->withScopes('profile', 'email', 'phone')
    ->uri();
```

To handle OAuth callbacks to get user info:

```php
$authenticator = new Authenticator($statePersistor, $logger)

$authorizationCodeResponse = $authenticator->handleAuthorizationCodeCallback($callbackUri);

// Get the provider id.
$providerId = $authorizationCodeResponse->getProvider()->getIdentifier();

// Fetch access token.
$accessTokenResponse = $authenticator->fetchAccessTokenWithAuthorizationCode($authorizationCodeResponse);

// Fetch user info.
$userInfo = $authenticator->fetchUserInformationWithAccessToken($accessTokenResponse);

// e.g. getting the user's subject identifier:
$subjectIdentifier = $userInfo->getSubjectIdentifier();
```
