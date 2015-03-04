<?php
namespace De\SWebhosting\DatabaseLog\Utility;
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
 * Utility for backtrace handling.
 *
 * @Flow\Scope("singleton")
 */
class BacktraceUtility {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * Returns backtrace data
	 *
	 * @param integer $backTraceOffset
	 * @return array Array containing detected package key, class name and method name (in this order).
	 */
	public function getBacktraceData($backTraceOffset) {

		$packageKey = NULL;
		$className = NULL;
		$methodName = NULL;

		$backtraceArray = debug_backtrace(FALSE);
		foreach ($backtraceArray as $backtraceData) {

			$methodName = isset($backtraceData['function']) ? $backtraceData['function'] : NULL;
			if (!isset($methodName)) {
				continue;
			}

			// Filter out some system methods.
			if ($methodName === '__call' || $methodName === 'call_user_func' || $methodName === 'call_user_func_array') {
				continue;
			}

			$className = isset($backtraceData['class']) ? $backtraceData['class'] : NULL;

			// Filter out system class names.
			if (isset($className) && strstr($className, 'DependencyProxy')) {
				continue;
			}

			if ($backTraceOffset > 0) {
				$backTraceOffset--;
				continue;
			}

			if (isset($className)) {

				// Cut off the _Original substring generated by Flow.
				if (substr($className, -9) === '_Original') {
					$className = substr($className, 0, strlen($className) - 9);
				}

				$packageKey = $this->packageManager->getPackageByClassName($className)->getPackageKey();
				break;
			}
		}

		$backtraceData = array($packageKey, $className, $methodName);
		return $backtraceData;
	}
}