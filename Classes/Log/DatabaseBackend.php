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

use De\SWebhosting\DatabaseLog\Domain\Repository\LogEntryFactory;
use De\SWebhosting\DatabaseLog\Domain\Repository\LogEntryRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Log\Backend\AbstractBackend;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Persistence\PersistenceManagerInterface;

/**
 * A Backend for storing logs in the database
 */
class DatabaseBackend extends AbstractBackend
{
    /**
     * @var Bootstrap
     * @Flow\Inject
     */
    protected $bootstrap;

    /**
     * @var LogEntryRepository
     * @Flow\Inject
     */
    protected $logEntryRepository;

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    /**
     * @var PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

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

        $logEntryFactory = $this->createLogEntryFactory($message, $severity);
        $logEntryFactory->logAdditionalData($additionalData);
        $logEntryFactory->logCodeLocation($packageKey, $className, $methodName);
        $logEntryFactory->enableIpAdressLogging($this->logIpAddress);

        $logEntry = $logEntryFactory->createLogEntry();

        $this->logEntryRepository->add($logEntry);
        $this->persistenceManager->persistAll();
    }

    /**
     * Carries out all actions necessary to cleanly close the logging backend, such as
     * closing the log file or disconnecting from a database.
     */
    public function close(): void
    {
        // Nothing to do
    }

    /**
     * Carries out all actions necessary to prepare the logging backend, such as opening
     * the log file or opening a database connection.
     */
    public function open(): void
    {
        // Nothing to do
    }

    /**
     * @param string $message
     * @param int $severity
     * @return LogEntryFactory
     */
    private function createLogEntryFactory(string $message, int $severity): LogEntryFactory
    {
        return new LogEntryFactory(
            $message,
            $severity,
            $this->bootstrap,
            $this->objectManager,
            $this->persistenceManager
        );
    }
}
