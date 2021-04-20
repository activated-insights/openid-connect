<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Dtos;

use Pinnacle\CommonValueObjects\EmailAddress;
use Pinnacle\OpenIdConnect\Exceptions\OAuthFailedException;
use stdClass;

class UserInfoDto
{
    private string       $subjectIdentifier;

    private string       $fullName;

    private EmailAddress $emailAddress;

    private bool         $emailAddressVerified;

    public function __construct(
        string $subjectIdentifier,
        string $fullName,
        EmailAddress $emailAddress,
        bool $emailAddressVerified
    ) {
        $this->subjectIdentifier    = $subjectIdentifier;
        $this->fullName             = $fullName;
        $this->emailAddress         = $emailAddress;
        $this->emailAddressVerified = $emailAddressVerified;
    }

    /**
     * @throws OAuthFailedException
     */
    public static function createWithJson(stdClass $json): self
    {
        if (!isset($json->sub) || !is_string($json->sub)) {
            throw new OAuthFailedException('The subject identifier of the user was not found.');
        }
        if (!isset($json->name) || !is_string($json->name)) {
            throw new OAuthFailedException('The name of the user was not found.');
        }
        if (!isset($json->email) || !is_string($json->email)) {
            throw new OAuthFailedException('The email address of the user was not found.');
        }

        // The email_verified value is not always returned. Handle as a special case.
        if (isset($json->email_verified)) {
            // Sometimes it's passed as a string value, even though the spec says it should be a boolean.
            if (!is_string($json->email_verified) && !is_bool($json->email_verified)) {
                throw new OAuthFailedException(
                    'The email verification value of the user was set, but is not a string or boolean value.'
                );
            }

            $emailVerified = (is_string($json->email_verified) && $json->email_verified === 'true') ||
                             $json->email_verified;
        } else {
            // No email_verified value found, set to false.
            $emailVerified = false;
        }

        return new self(
            $json->sub,
            $json->name,
            new EmailAddress($json->email),
            $emailVerified
        );
    }

    public function getSubjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function isEmailAddressVerified(): bool
    {
        return $this->emailAddressVerified;
    }
}
