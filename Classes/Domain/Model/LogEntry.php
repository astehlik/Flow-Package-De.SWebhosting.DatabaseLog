<?php
declare(strict_types=1);

namespace De\SWebhosting\DatabaseLog\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package                          *
 * "De.SWebhosting.DatabaseLog".                                          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * A log entry
 *
 * @Flow\Scope("prototype")
 * @Flow\Entity
 */
class LogEntry
{
    /**
     * The identifier of the account that triggered the log entry.
     *
     * This is usefull if the user record was deleted.
     *
     * @var string
     */
    protected $accountIdentifier = '';

    /**
     * Array containing additional log data.
     *
     * @ORM\Column(nullable=true)
     * @var array
     */
    protected $additionalData;

    /**
     * An account identifier always belongs to an authentication provider.
     * To find out which account was used when the log entry was created it
     * is essential to also know the authentication provider to which
     * the account identifier belongs.
     *
     * @var string
     */
    protected $authenticationProviderName = '';

    /**
     * The class that created this log message
     *
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $className;

    /**
     * The time when the log entry was created
     *
     * @var DateTime
     */
    protected $dateTime;

    /**
     * The IP address from where the request was made that caused the log entry
     *
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $ipAddress;

    /**
     * Message of the log entry.
     *
     * @var string
     */
    protected $message;

    /**
     * The method that created this log message.
     *
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $methodName;

    /**
     * The package that created this log message.
     *
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $packageKey;

    /**
     * The severity of the log entry
     *
     * @var int
     */
    protected $severity;

    /**
     * The user that triggered the log entry.
     *
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $userFullName;

    /**
     * We intentionally do not use the User object here to be independent from the party framework.
     *
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $userObjectIdentifier;

    public function __construct(
        string $message,
        int $severity,
        ?array $additionalData,
        ?string $packageKey,
        ?string $className,
        ?string $methodName,
        ?string $ipAddress,
        string $accountIdentifier,
        string $authenticationProviderName,
        ?string $userObjectIdentifier,
        ?string $userFullName
    ) {
        $this->dateTime = new DateTime();
        $this->message = $message;
        $this->severity = $severity;
        $this->additionalData = $additionalData;
        $this->packageKey = $packageKey;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->ipAddress = $ipAddress;
        $this->accountIdentifier = $accountIdentifier;
        $this->authenticationProviderName = $authenticationProviderName;
        $this->userObjectIdentifier = $userObjectIdentifier;
        $this->userFullName = $userFullName;
    }

    public function getAccountIdentifier(): string
    {
        return $this->accountIdentifier;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function getAuthenticationProviderName(): string
    {
        return $this->authenticationProviderName;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMethodName(): ?string
    {
        return $this->methodName;
    }

    public function getPackageKey(): ?string
    {
        return $this->packageKey;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

    public function getUserFullName(): ?string
    {
        return $this->userFullName;
    }

    public function getUserObjectIdentifier(): ?string
    {
        return $this->userObjectIdentifier;
    }
}
