<?php
namespace De\SWebhosting\DatabaseLog\Log;

/*                                                                        *
 * This script belongs to the FLOW3 package "DatabaseLog".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Marker interface for the tape archive logger.
 */
interface UserActionLoggerInterface extends \TYPO3\Flow\Log\LoggerInterface {

	/**
	 * @abstract
	 * @param string $message
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 * @param int $severity
	 * @param array $additionalData
	 * @param string $packageKey
	 * @param string $className
	 * @param string $methodName
	 * @return void
	 */
	public function logUserAction($message, $user, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL);
}

?>