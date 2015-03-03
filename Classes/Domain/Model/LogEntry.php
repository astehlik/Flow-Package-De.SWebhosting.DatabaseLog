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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Party\Domain\Service\PartyService;

/**
 * A log entry
 *
 * @Flow\Scope("prototype")
 * @Flow\Entity
 */
class LogEntry {


	/**
	 * The identifier of the account that triggered the log entry.
	 *
	 * This is usefull if the user record was deleted.
	 *
	 * @var string
	 */
	protected $accountIdentifier = '';

	/**
	 * Array containing additional log data, will be used by the translation system
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
	 * @var \DateTime
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
	 * Message of the log entry, can be a translation key
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * The method that created this log message
	 *
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	protected $methodName;

	/**
	 * @Flow\Inject
	 * @Flow\Transient
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * The package that created this log message
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $packageKey;

	/**
	 * @Flow\Inject
	 * @Flow\Transient
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @Flow\Inject
	 * @Flow\Transient
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

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
	public function getIpAddress() {
		return $this->ipAddress;
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
	 * Sets the account properties and if the account has a related user it will
	 * also set the user properties when the the party package is available.
	 *
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return void
	 */
	public function setAccount($account) {

		$this->accountIdentifier = (string)$account->getAccountIdentifier();
		$this->authenticationProviderName = (string)$account->getAuthenticationProviderName();

		if ($this->packageManager->isPackageActive('TYPO3.Party')) {
			/** @var PartyService $partyService */
			$partyService = $this->objectManager->get(PartyService::class);
			$party = $partyService->getAssignedPartyOfAccount($account);
			if (isset($party)) {
				$this->setUser($party);
			}
		}
	}

	/**
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress) {
		$this->ipAddress = $ipAddress;
	}

	/**
	 * If the user is not NULL the userObjectIdentifier property will be set
	 * to the object identifier of the given user. Additionally setUserFullName
	 * will be called.
	 *
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 * @return void
	 */
	protected function setUser($user) {

		if (!isset($user)) {
			$this->userObjectIdentifier = NULL;
			$this->userFullName = NULL;
			return;
		}

		$this->userObjectIdentifier = $this->persistenceManager->getIdentifierByObject($user);
		$this->setUserFullName($user);
	}

	/**
	 * Sets the userFullName property if the given user is an instance
	 * of Person and has an associated name. Otherwise the property
	 * is reset to NULL.
	 *
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 * @return void
	 */
	protected function setUserFullName($user) {

		$this->userFullName = NULL;

		if (
			!isset($user)
			|| !$user instanceof \TYPO3\Party\Domain\Model\Person
		) {
			return;
		}

		$personName = $user->getName();
		if (isset($personName)) {
			$this->userFullName = $personName->getFullName();
		}
	}
}
