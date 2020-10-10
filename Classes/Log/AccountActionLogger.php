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
 * Logger for tracing actions connected to an account.
 */
class AccountActionLogger implements AccountActionLoggerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function injectLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Writes a message in the log and adds the given party to the additional data.
     *
     * When the DatabaseBackend is used, the user will be extracted from the additional data
     * and a relation to the party table will be stored.
     *
     * @param mixed $level
     * @param string $message The log message.
     * @param array $context Optional additional data in an array.
     * @param Account $account The account connected to this log entry.
     * @return void
     */
    public function logAccountAction(
        $level,
        string $message,
        ?Account $account = null,
        array $context = []
    ): void {
        if (isset($account)) {
            $context['De.SWebhosting.DatabaseLog.Account'] = $account;
        }

        $this->logger->log($level, $message, $context);
    }
}
