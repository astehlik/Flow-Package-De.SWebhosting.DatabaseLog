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

/**
 * Interface for logger for tracing actions connected to an account.
 */
interface AccountActionLoggerInterface
{
    /**
     * @param mixed $level
     * @param string $message The log message.
     * @param array $context Optional additional data in an array.
     * @param Account $account The account connected to this log entry.
     */
    public function logAccountAction(
        $level,
        string $message,
        Account $account = null,
        array $context = []
    ): void;
}
