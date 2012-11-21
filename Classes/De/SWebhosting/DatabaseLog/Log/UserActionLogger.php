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

use TYPO3\Flow\Annotations as FLOW3;

/**
 * The tape archive logger, based on the user action logger
 */
class UserActionLogger extends \TYPO3\Flow\Log\Logger implements UserActionLoggerInterface {

	/**
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * Writes a message in the log and adds the given party to the additional data.
	 *
	 * When the DatabaseBackend is used, the user will be extracted from the additional data
	 * and a relation to the party table will be stored.
	 *
	 * @param string $message
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 * @param int $severity
	 * @param array $additionalData
	 * @param string $packageKey
	 * @param string $className
	 * @param string $methodName
	 * @param int $backTraceDepth
	 * @return void
	 */
	public function logUserAction($message, $user, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL, $backTraceDepth = 1) {

		if (isset($user)) {
			$additionalData['userFromUserActionLog'] = $user;
		}

		if ($packageKey === NULL) {
			list($packageKey, $className, $methodName) = $this->getBacktraceData($backTraceDepth + 1);
		}

		$this->log($message, $severity, $additionalData, $packageKey, $className, $methodName);
	}

	/**
	 * Returns backtrace data
	 *
	 * @param int $backTraceDepth
	 * @return array
	 */
	protected function getBacktraceData($backTraceDepth) {

		$backtrace = debug_backtrace(FALSE);

		$className = isset($backtrace[$backTraceDepth]['class']) ? $backtrace[$backTraceDepth]['class'] : NULL;
		$methodName = isset($backtrace[$backTraceDepth]['function']) ? $backtrace[$backTraceDepth]['function'] : NULL;
		$packageKey = $this->getPackageKeyByClassName($className);

		$backtraceData = array($packageKey, $className, $methodName);

		return $backtraceData;
	}

	/**
	 * Tries to determine the package name from the class namespace by checking
	 * all namespaces for the Package class. If it is found the namespace parts
	 * will be imploded with dots.
	 *
	 * @param string $className
	 * @return string The package name or an empty string if the package can not be determined
	 */
	protected function getPackageKeyByClassName($className) {

		$classParts = explode('\\', $className);
		$classPartCount = count($classParts) - 1;

		while ($classPartCount > 0) {

			$packageNamespaceParts = array_slice($classParts, 0, $classPartCount);
			$packageNamespace = implode('\\', $packageNamespaceParts);
			$packageClassName = $packageNamespace . '\\Package';

			if (class_exists($packageClassName)) {

				$packageClassParents = class_parents($packageClassName);

				if (array_key_exists('TYPO3\\FLOW3\\Package\\Package', $packageClassParents)) {
					return implode('.', $packageNamespaceParts);
				}
			}

			$classPartCount--;
		}

		return '';
	}
}
?>