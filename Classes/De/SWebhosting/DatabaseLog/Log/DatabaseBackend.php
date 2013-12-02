<?php
namespace De\SWebhosting\DatabaseLog\Log;

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

/**
 * A Backend for storing logs in the database
 */
class DatabaseBackend extends \TYPO3\Flow\Log\Backend\AbstractBackend {

	/**
	 * @var \De\SWebhosting\DatabaseLog\Domain\Repository\LogEntryRepository
	 * @FLOW3\Inject
	 */
	protected $logEntryRepository;

	/**
	 * Carries out all actions necessary to prepare the logging backend, such as opening
	 * the log file or opening a database connection.
	 *
	 * @return void
	 */
	public function open() {
		// nothing to do
	}

	/**
	 * Appends the given message along with the additional information into the log.
	 *
	 * @param string $message The message to log
	 * @param int $severity One of the LOG_* constants
	 * @param mixed $additionalData A variable containing more information about the event to be logged
	 * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
	 * @param string $className Name of the class triggering the log (determined automatically if not specified)
	 * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
	 * @throws \InvalidArgumentException If the additionalData parameter set, but not an array
	 * @return void
	 */
	public function append($message, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL) {

		if ($severity > $this->severityThreshold) {
			return;
		}

		$user = NULL;

		if (isset($additionalData)) {

			if (!is_array($additionalData)) {
				throw new \InvalidArgumentException('For the database backend the additional data needs to be an array');
			}

			if (array_key_exists('userFromUserActionLog', $additionalData)) {

				$possibleUser = $additionalData['userFromUserActionLog'];
				unset($additionalData['userFromUserActionLog']);

				if ($possibleUser instanceof \TYPO3\Party\Domain\Model\AbstractParty) {
					$user = $possibleUser;
				}
			}

			if (!count($additionalData)) {
				$additionalData = NULL;
			}
		}

		$logEntry = new \De\SWebhosting\DatabaseLog\Domain\Model\LogEntry($message, $severity, $additionalData, $packageKey, $className, $methodName);

		if (isset($user)) {
			$logEntry->setUser($user);
		}

		$ipAddress = ($this->logIpAddress === TRUE) ? str_pad((isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''), 15) : '';
		$logEntry->setIpAddress($ipAddress);

		$this->logEntryRepository->add($logEntry);
	}

	/**
	 * Carries out all actions necessary to cleanly close the logging backend, such as
	 * closing the log file or disconnecting from a database.
	 *
	 * @return void
	 */
	public function close() {
		// nothing to do
	}
}
