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

use TYPO3\Flow\Log\LoggerInterface;

/**
 * Marker interface for the tape archive logger.
 */
interface AccountActionLoggerInterface extends LoggerInterface {

	/**
	 * @abstract
	 * @param string $message
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 * @param int $severity
	 * @param array $additionalData
	 * @param string $packageKey
	 * @param string $className
	 * @param string $methodName
	 * @param int $backTraceOffset
	 * @return void
	 */
	public function logAccountAction($message, $user, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL, $backTraceOffset = 0);
}