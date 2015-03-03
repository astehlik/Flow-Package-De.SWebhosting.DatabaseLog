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

use TYPO3\Flow\Annotations as Flow;

/**
 * The tape archive logger, based on the user action logger
 */
class AccountActionLogger extends \TYPO3\Flow\Log\Logger implements AccountActionLoggerInterface {

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
	 * @param \TYPO3\Flow\Security\Account $account
	 * @param int $severity
	 * @param array $additionalData
	 * @param string $packageKey
	 * @param string $className
	 * @param string $methodName
	 * @param int $backTraceOffset
	 * @return void
	 */
	public function logAccountAction($message, $account, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL, $backTraceOffset = 0) {

		if (isset($account)) {
			$additionalData['De.SWebhosting.DatabaseLog.Account'] = $account;
		}

		if ($packageKey === NULL) {
			// We add plus two because we do not want the logAccountAction() or the
			// getFirstClassFromBacktrace() method to appear in the backtrace
			list($packageKey, $className, $methodName) = $this->getFirstClassFromBacktrace($backTraceOffset + 2);
		}

		$this->log($message, $severity, $additionalData, $packageKey, $className, $methodName);
	}

	/**
	 * Returns backtrace data
	 *
	 * @param integer $backTraceOffset
	 * @return array
	 */
	protected function getFirstClassFromBacktrace($backTraceOffset) {

		$packageKey = NULL;
		$className = NULL;
		$methodName = NULL;

		$backtraceArray = debug_backtrace(FALSE);
		foreach ($backtraceArray as $backtraceData) {

			$methodName = isset($backtraceData['function']) ? $backtraceData['function'] : NULL;
			if (!isset($methodName)) {
				continue;
			}

			// Filter out some system methods
			if ($methodName === '__call' || $methodName === 'call_user_func' || $methodName === 'call_user_func_array') {
				continue;
			}

			if ($backTraceOffset > 0) {
				$backTraceOffset--;
				continue;
			}

			$className = isset($backtraceData['class']) ? $backtraceData['class'] : NULL;
			if (isset($className)) {

				// Filter out system class names
				if (strstr($className, 'DependencyProxy')) {
					continue;
				}

				$packageKey = $this->getPackageKeyByClassName($className);
				break;
			}
		}

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