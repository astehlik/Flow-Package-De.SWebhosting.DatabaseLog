<?php
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

use TYPO3\Flow\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A log entry
 *
 * @FLOW3\Scope("prototype")
 * @FLOW3\Entity
 */
class LogEntry {


	/**
	 * The identifier of the account that triggered the log entry.
	 *
	 * This is usefull if the user record was deleted.
	 *
	 * @var string
	 */
	protected $accountIdentifier;

	/**
	 * Array containing additional log data, will be used by the translation system
	 *
	 * @var array
	 * @ORM\Column(nullable=true)
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
	protected $authenticationProviderName;

	/**
	 * The class that created this log message
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $className;

	/**
	 * The time when the log entry was created
	 *
	 * @var \DateTime
	 */
	protected $dateTime;

	/**
	 * The IP address from where the request was made that caused the log entry
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $ipAddress;

	/**
	 * Message of the log entry, can be a translation key
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * The method that created this log message
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $methodName;

	/**
	 * The package that created this log message
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
	 * The user that triggered the log entry
	 *
	 * @var \TYPO3\Party\Domain\Model\AbstractParty
	 * @ORM\ManyToOne
	 * @ORM\Column(nullable=true)
	 */
	protected $user;

	/**
	 * Creates a new log entry
	 *
	 * @param string $message
	 * @param int $severity
	 * @param array $additionalData
	 * @param string $packageKey
	 * @param string $className
	 * @param string $methodName
	 */
	public function __construct($message, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL) {
		$this->dateTime = new \DateTime();
		$this->message = $message;
		$this->severity = $severity;
		$this->additionalData = $additionalData;
		$this->packageKey = $packageKey;
		$this->className = $className;
		$this->methodName = $methodName;
		$this->ipAddress = NULL;
	}

	/**
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->additionalData;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTime() {
		return $this->dateTime;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getMethodName() {
		return $this->methodName;
	}

	/**
	 * @return string
	 */
	public function getPackageKey() {
		return $this->packageKey;
	}

	/**
	 * @return int
	 */
	public function getSeverity() {
		return $this->severity;
	}

	/**
	 * @return \TYPO3\Party\Domain\Model\AbstractParty
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress) {
		$this->ipAddress = $ipAddress;
	}

	/**
	 * @param \TYPO3\Flow\Security\Account $account
	 */
	public function setAccount($account) {
		$this->accountIdentifier = $account->getAccountIdentifier();
		$this->authenticationProviderName = $account->getAuthenticationProviderName();
		$this->user = $account->getParty();
	}
}
