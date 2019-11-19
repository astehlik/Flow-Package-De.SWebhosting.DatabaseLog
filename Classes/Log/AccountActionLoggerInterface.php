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

use Neos\Flow\Security\Account;
use Psr\Log\LoggerInterface;

/**
 * Interface for logger for tracing actions connected to an account.
 */
interface AccountActionLoggerInterface extends LoggerInterface
{
    /**
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
    );
}
