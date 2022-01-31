<?php

namespace Unit\Authentication\StatePersister;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Constants\StateKey;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Exceptions\StatePersisterMissingValueException;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\StatePersisterWrapper;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;

class StatePersisterWrapperTest extends TestCase
{
    /**
     * @test
     */
    public function storeChallenge_WithChallenge_CallsExpectedInterfaceFunctions(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();
        $challenge     = Challenge::createWithRandomString();

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        // Assert
        $mockPersister->expects($this->once())
                      ->method('storeValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::CHALLENGE()->getValue(),
                          $challenge->getValue()
                      );

        // Act
        $statePersisterWrapper->storeChallenge($challenge);
    }

    /**
     * @test
     */
    public function getChallenge_StoreHasChallenge_ReturnsChallenge(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();
        $challenge     = Challenge::createWithRandomString();

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->expects($this->once())
                      ->method('getValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::CHALLENGE()->getValue()
                      )
                      ->willReturn($challenge->getValue());

        // Act
        $returnedChallenge = $statePersisterWrapper->getChallenge();

        // Assert
        $this->assertSame($challenge->getValue(), $returnedChallenge->getValue());
    }

    /**
     * @test
     */
    public function getChallenge_StoreMissingChallenge_ThrowsException(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->expects($this->once())
                      ->method('getValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::CHALLENGE()->getValue()
                      )
                      ->willReturn(null);

        // Assert
        $this->expectException(StatePersisterMissingValueException::class);

        // Act
        $statePersisterWrapper->getChallenge();
    }

    /**
     * @test
     */
    public function storeProvider_WithProvider_CallsExpectedInterfaceFunctions(): void
    {
        // Assemble
        $mockPersister         = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state                 = State::createWithRandomString();
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        // Assert
        $mockPersister->expects($this->exactly(5))
                      ->method('storeValue')
                      ->withConsecutive(
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_IDENTIFIER()->getValue(),
                              $identifier->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_ID()->getValue(),
                              $clientId->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_SECRET()->getValue(),
                              $clientSecret->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_AUTHORIZATION_ENDPOINT()->getValue(),
                              (string)$authorizationEndpoint,
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_TOKEN_ENDPOINT()->getValue(),
                              (string)$tokenEndpoint,
                          ],
                      );

        // Act
        $statePersisterWrapper->storeProvider($provider);
    }

    /**
     * @test
     */
    public function getProvider_StoreHasProvider_ReturnsProvider(): void
    {
        // Assemble
        $mockPersister         = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state                 = State::createWithRandomString();
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->method('getValue')
                      ->withConsecutive(
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_IDENTIFIER()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_ID()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_SECRET()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_AUTHORIZATION_ENDPOINT()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_TOKEN_ENDPOINT()->getValue(),
                          ],
                      )
                      ->willReturnOnConsecutiveCalls(
                          $identifier->getValue(),
                          $clientId->getValue(),
                          $clientSecret->getValue(),
                          (string)$authorizationEndpoint,
                          (string)$tokenEndpoint,
                      );

        // Act
        $returnedProvider = $statePersisterWrapper->getProvider();

        // Assert
        $this->assertEquals($provider->getIdentifier(), $returnedProvider->getIdentifier());
        $this->assertEquals($provider->getClientId(), $returnedProvider->getClientId());
        $this->assertEquals($provider->getClientSecret(), $returnedProvider->getClientSecret());
        $this->assertEquals(
            (string)$provider->getAuthorizationEndpoint(),
            (string)$returnedProvider->getAuthorizationEndpoint()
        );
        $this->assertEquals((string)$provider->getTokenEndpoint(), (string)$returnedProvider->getTokenEndpoint());
    }

    /**
     * @test
     */
    public function getProvider_StoreMissingProvider_ThrowsException(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->method('getValue')
                      ->withConsecutive(
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_IDENTIFIER()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_ID()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_CLIENT_SECRET()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_AUTHORIZATION_ENDPOINT()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_TOKEN_ENDPOINT()->getValue(),
                          ],
                          [
                              $state->getValue() . '.' . StateKey::PROVIDER_USER_INFO_ENDPOINT()->getValue(),
                          ],
                      )
                      ->willReturnOnConsecutiveCalls(
                          null,
                          null,
                          null,
                          null,
                          null,
                          null,
                      );

        // Assert
        $this->expectException(StatePersisterMissingValueException::class);

        // Act
        $statePersisterWrapper->getProvider();
    }

    /**
     * @test
     */
    public function storeRedirectUri_WithRedirectUri_CallsExpectedInterfaceFunctions(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();
        $redirectUri   = new Uri('https://uri.test/redirect-uri');

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        // Assert
        $mockPersister->expects($this->once())
                      ->method('storeValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::REDIRECT_URI()->getValue(),
                          (string)$redirectUri
                      );

        // Act
        $statePersisterWrapper->storeRedirectUri($redirectUri);
    }

    /**
     * @test
     */
    public function getRedirectUri_StoreHasRedirectUri_ReturnsRedirectUri(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();
        $redirectUri   = new Uri('https://uri.test/redirect-uri');

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->expects($this->once())
                      ->method('getValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::REDIRECT_URI()->getValue()
                      )
                      ->willReturn((string)$redirectUri);

        // Act
        $returnedRedirectUri = $statePersisterWrapper->getRedirectUri();

        // Assert
        $this->assertSame((string)$redirectUri, (string)$returnedRedirectUri);
    }

    /**
     * @test
     */
    public function getRedirectUri_StoreMissingRedirectUri_ThrowsException(): void
    {
        // Assemble
        $mockPersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();
        $state         = State::createWithRandomString();

        $statePersisterWrapper = new StatePersisterWrapper($mockPersister, $state);

        $mockPersister->expects($this->once())
                      ->method('getValue')
                      ->with(
                          $state->getValue() . '.' . StateKey::REDIRECT_URI()->getValue()
                      )
                      ->willReturn(null);

        // Assert
        $this->expectException(StatePersisterMissingValueException::class);

        // Act
        $statePersisterWrapper->getRedirectUri();
    }
}
