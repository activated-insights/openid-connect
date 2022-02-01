<?php

namespace Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\InvalidUserIdTokenException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\MissingRequiredClaimKeyException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\UserIdTokenHasExpiredException;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\Constants\ClaimKey;

class UserIdToken
{
    /**
     * @var array
     */
    private array             $parsedValues;

    private Uri               $issuerIdentifier;

    private SubjectIdentifier $subjectIdentifier;

    private Audiences         $audiences;

    private int               $expirationTime;

    private int               $issuedTime;

    /**
     * @throws InvalidUserIdTokenException
     * @throws MissingRequiredClaimKeyException
     * @throws UserIdTokenHasExpiredException
     */
    public function __construct(private string $token)
    {
        if (trim($this->token) === '') {
            throw new InvalidUserIdTokenException(
                sprintf('%s was provided an empty string in the constructor.', self::class)
            );
        }

        $this->parsedValues = self::parseToken();

        $this->assertParsedValuesContainRequiredClaims();

        $this->issuerIdentifier  = new Uri($this->findClaimByKey(ClaimKey::ISSUER_IDENTIFIER()->getValue()));
        $this->subjectIdentifier = new SubjectIdentifier(
            $this->findClaimByKey(ClaimKey::SUBJECT_IDENTIFIER()->getValue())
        );
        $this->audiences         = new Audiences($this->findClaimByKey(ClaimKey::AUDIENCES()->getValue()));
        $this->expirationTime    = (int)$this->findClaimByKey(ClaimKey::EXPIRATION_TIME()->getValue());
        $this->issuedTime        = (int)$this->findClaimByKey(ClaimKey::ISSUED_TIME()->getValue());

        $this->assertTokenHasNotExpired();
    }

    /**
     * @return Uri
     */
    public function getIssuerIdentifier(): Uri
    {
        return $this->issuerIdentifier;
    }

    /**
     * @return SubjectIdentifier
     */
    public function getSubjectIdentifier(): SubjectIdentifier
    {
        return $this->subjectIdentifier;
    }

    /**
     * @return Audiences
     */
    public function getAudiences(): Audiences
    {
        return $this->audiences;
    }

    /**
     * @return int
     */
    public function getExpirationTime(): int
    {
        return $this->expirationTime;
    }

    /**
     * @return int
     */
    public function getIssuedTime(): int
    {
        return $this->issuedTime;
    }

    public function findClaimByKey(string $key): mixed
    {
        if (!$this->hasClaimKey($key)) {
            return null;
        }

        return $this->parsedValues[$key];
    }

    public function hasClaimKey(string $key): bool
    {
        return isset($this->parsedValues[$key]);
    }

    /**
     * @throws MissingRequiredClaimKeyException
     */
    private function assertParsedValuesContainRequiredClaims(): void
    {
        foreach (ClaimKey::requiredClaimKeys() as $requiredClaimKey) {
            if (!isset($this->parsedValues[$requiredClaimKey->getValue()])) {
                throw new MissingRequiredClaimKeyException($requiredClaimKey);
            }
        }
    }

    /**
     * @throws UserIdTokenHasExpiredException
     */
    private function assertTokenHasNotExpired(): void
    {
        $currentTime = time();

        if ($currentTime >= $this->expirationTime) {
            throw new UserIdTokenHasExpiredException(
                sprintf(
                    'The user id token has expired. expirationTime: %u, currentTime %u',
                    $this->expirationTime,
                    $currentTime
                )
            );
        }
    }

    /**
     * @throws InvalidUserIdTokenException
     */
    private function parseToken(): array
    {
        $tokenSections = explode('.', $this->token);

        if (count($tokenSections) !== 3) {
            throw new InvalidUserIdTokenException('Provided token has an invalid number of sections.');
        }

        $payloadBase64String = str_replace(['-', '_'], ['+', '/'], $tokenSections[1]);

        $payloadJsonString = base64_decode($payloadBase64String);

        if ($payloadJsonString === false) {
            throw new InvalidUserIdTokenException(sprintf('Unable to parse base64 string %s.', $payloadBase64String));
        }

        $payload = json_decode($payloadJsonString);

        if ($payload === null) {
            throw new InvalidUserIdTokenException(sprintf('Unable to parse JSON string %s.', $payloadJsonString));
        }

        return (array)$payload;
    }
}
