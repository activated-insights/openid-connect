<h1 align="center">Openid-Connect</h1>

[![Basic Continuous Integration](https://github.com/pinnacleqi/openid-connect/actions/workflows/basic-continuous-integration.yml/badge.svg)](https://github.com/pinnacleqi/openid-connect/actions/workflows/basic-continuous-integration.yml)

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
$providerConfiguration = new ProviderConfiguration(
        $identifier, // Optional parameter used to identify the provider within the application.
        $clientId,
        $clientSecret,
        $authorizationEndpoint,
        $tokenEndpoint
);

$authenticator =  new Authenticator(
    $statePersistor, // Class should implement StatePersisterInterface.
    $logger // Class should implement the LoggerInterface.
);

$authenticationRedirectUri = $authenticator
    ->beginAuthentication($providerConfiguration, $redirectUrl)
    ->withScopes('profile', 'email', 'phone')
    ->uri();
```

To handle OAuth callbacks to get user info:

```php
$authenticator = new Authenticator($statePersistor, $logger)

$authorizationCodeResponse = $authenticator->handleAuthorizationCodeCallback($callbackUri);

// Fetch tokens.
$tokensResponse = $authenticator->fetchTokensWithAuthorizationCode($authorizationCodeResponse);

// Get provider identifier that was passed with the initial configuration.
$providerId = $tokensResponse->getProvider()->getIdentifier();

// Get access_token.
$accessToken = $tokensResponse->getAccessToken();

// Get id_token.
$userIdToken = $tokensResponse->getUserIdToken();

// Get subject identifier from id_token.
$subjectIdentifier = $userIdToken->getSubjectIdentifier();

// Check if a claim key exists in the id_token.
$claimExists = $userIdToken->hasClaimKey('foo');

// Access claims from the id_token (Returns null if the requested claim cannot be found).
$nameClaim  = $userIdToken->findClaimByKey('name');
$emailClaim = $userIdToken->findClaimByKey('email');


```
