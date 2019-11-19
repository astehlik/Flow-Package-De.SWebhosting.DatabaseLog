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

use De\SWebhosting\DatabaseLog\Domain\Model\LogEntry;
use De\SWebhosting\DatabaseLog\Domain\Repository\LogEntryRepository;
use InvalidArgumentException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Backend\AbstractBackend;
use Neos\Flow\Security\Account;

/**
 * A Backend for storing logs in the database
 */
class DatabaseBackend extends AbstractBackend
{
    /**
     * @var LogEntryRepository
     * @Flow\Inject
     */
    protected $logEntryRepository;

    /**
     * Appends the given message along with the additional information into the log.
     *
     * @param string $message The message to log
     * @param int $severity One of the LOG_* constants
     * @param mixed $additionalData A variable containing more information about the event to be logged
     * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
     * @param string $className Name of the class triggering the log (determined automatically if not specified)
     * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
     */
    public function append(
        string $message,
        int $severity = LOG_INFO,
        $additionalData = null,
        string $packageKey = null,
        string $className = null,
        string $methodName = null
    ): void {
        if ($severity > $this->severityThreshold) {
            return;
        }

        $account = null;

        if (isset($additionalData)) {

            if (!is_array($additionalData)) {
                throw new InvalidArgumentException(
                    'For the database backend the additional data needs to be an array'
                );
            }

            if (array_key_exists('De.SWebhosting.DatabaseLog.Account', $additionalData)) {

                $possibleAccount = $additionalData['De.SWebhosting.DatabaseLog.Account'];
                unset($additionalData['De.SWebhosting.DatabaseLog.Account']);

                if ($possibleAccount instanceof Account) {
                    $account = $possibleAccount;
                }
            }

            if (!count($additionalData)) {
                $additionalData = null;
            }
        }

        $logEntry = new LogEntry($message, $severity, $additionalData, $packageKey, $className, $methodName);

        if (isset($account)) {
            $logEntry->setAccount($account);
        }

        $ipAddress = $this->getIpAddress();
        $logEntry->setIpAddress($ipAddress);

        $this->logEntryRepository->add($logEntry);
    }

    /**
     * Carries out all actions necessary to cleanly close the logging backend, such as
     * closing the log file or disconnecting from a database.
     */
    public function close(): void
    {
        // nothing to do
    }

    /**
     * Carries out all actions necessary to prepare the logging backend, such as opening
     * the log file or opening a database connection.
     */
    public function open(): void
    {
        // nothing to do
    }

    /**
     * @return string
     */
    protected function getIpAddress(): string
    {
        if (!$this->logIpAddress) {
            return '';
        }

        $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        return str_pad($remoteAddress, 15);
    }
}
