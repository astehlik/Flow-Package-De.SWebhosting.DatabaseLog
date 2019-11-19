<?php
declare(strict_types=1);

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

use De\SWebhosting\DatabaseLog\Utility\BacktraceUtility;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Psr\Logger;
use Neos\Flow\Security\Account;

/**
 * Logger for tracing actions connected to an account.
 */
class AccountActionLogger extends Logger implements AccountActionLoggerInterface
{
    /**
     * @Flow\Inject
     * @var BacktraceUtility
     */
    protected $backtraceUtility;

    /**
     * Writes a message in the log and adds the given party to the additional data.
     *
     * When the DatabaseBackend is used, the user will be extracted from the additional data
     * and a relation to the party table will be stored.
     *
     * @param string $message The log message.
     * @param Account $account The account connected to this log entry.
     * @param int $severity The severity of the log entry.
     * @param array $additionalData Optional additional data in an array.
     * @param string $packageKey The package key from which the logging was triggered.
     * @param string $className The class name from which the logging was triggered.
     * @param string $methodName The method name from which the logging was triggered.
     * @param int $backTraceOffset If the package key / class name / method name are autodetected,
     *        this value can be used to modify the offset that is used when reading these values
     *        from a debug_backtrace().
     * @return void
     */
    public function logAccountAction(
        $message,
        $account,
        $severity = LOG_INFO,
        $additionalData = null,
        $packageKey = null,
        $className = null,
        $methodName = null,
        $backTraceOffset = 0
    ) {
        if (isset($account)) {
            $context['De.SWebhosting.DatabaseLog.Account'] = $account;
        }

        if ($packageKey === null || $className === null || $methodName === null) {
            // We add plus two because we do not want the logAccountAction() or the getBacktraceData() method to appear in the backtrace.
            list($detectedPackageKey, $detectedClassName, $detectedMethodName) = $this->backtraceUtility->getBacktraceData(
                $backTraceOffset + 2
            );
            $packageKey = $packageKey === null ? $detectedPackageKey : $packageKey;
            $className = $className === null ? $detectedClassName : $className;
            $methodName = $methodName === null ? $detectedMethodName : $methodName;
        }

        $context['FLOW_LOG_ENVIRONMENT']['packageKey'] = $packageKey;
        $context['FLOW_LOG_ENVIRONMENT']['className'] = $className;
        $context['FLOW_LOG_ENVIRONMENT']['methodName'] = $methodName;

        $this->log($message, $severity, $context);
    }
}
