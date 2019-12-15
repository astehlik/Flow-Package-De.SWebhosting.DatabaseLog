<?php
declare(strict_types=1);

namespace De\SWebhosting\DatabaseLog\Tests\Functional\Log;

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
use De\SWebhosting\DatabaseLog\Log\AccountActionLogger;
use De\SWebhosting\DatabaseLog\Log\AccountActionLoggerInterface;
use Neos\Flow\Log\PsrLoggerFactoryInterface;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Tests\FunctionalTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Tests for the AccountActionLogger class.
 */
class AccountActionLoggerTest extends FunctionalTestCase
{
    /**
     * @var boolean
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * @var AccountActionLoggerInterface
     */
    protected $accountActionLogger;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var LoggerInterface
     */
    protected $databaseLogger;

    /**
     * @var LogEntryRepository
     */
    protected $logEntryRepository;

    /**
     * @var string
     */
    protected $testMessage = 'TestMessage';

    /**
     * Initializes the action logger.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->persistenceManager instanceof PersistenceManager) {
            $this->markTestSkipped('Doctrine persistence is not enabled');
        }

        $this->accountRepository = $this->objectManager->get(AccountRepository::class);
        $this->logEntryRepository = $this->objectManager->get(LogEntryRepository::class);
        $this->databaseLogger = $this->objectManager->get(PsrLoggerFactoryInterface::class)->get('databaseLogger');
        $this->accountActionLogger = $this->objectManager->get(AccountActionLoggerInterface::class);
    }

    /**
     * @test
     */
    public function accountIsStoredAsExcpected()
    {
        $testAccount = $this->createTestAccount();
        $this->accountActionLogger->logAccountAction(
            LogLevel::INFO,
            $this->testMessage,
            $testAccount,
            LogEnvironment::fromMethodName(__METHOD__)
        );
        $this->persistenceManager->persistAll();

        $entries = $this->logEntryRepository->findAll();
        $this->assertEquals(1, $entries->count());

        /** @var LogEntry $logEntry */
        $logEntry = $entries->getFirst();
        $this->assertEquals(LOG_INFO, $logEntry->getSeverity());
        $this->assertEquals($this->testMessage, $logEntry->getMessage());
        $this->assertEquals($testAccount->getAccountIdentifier(), $logEntry->getAccountIdentifier());
        $this->assertEquals($testAccount->getAuthenticationProviderName(), $logEntry->getAuthenticationProviderName());

        $this->assertEquals($this->testMessage, $logEntry->getMessage());
        $this->assertEquals(__FUNCTION__, $logEntry->getMethodName());
        $this->assertEquals(__CLASS__, $logEntry->getClassName());
        $this->assertEquals('De.SWebhosting.DatabaseLog', $logEntry->getPackageKey());
    }

    /**
     * @test
     */
    public function logDataIsStoredAsExpected()
    {
        $this->databaseLogger->info($this->testMessage, LogEnvironment::fromMethodName(__METHOD__));
        $this->persistenceManager->persistAll();
        $entries = $this->logEntryRepository->findAll();
        $this->assertEquals(1, $entries->count());

        /** @var LogEntry $logEntry */
        $logEntry = $entries->getFirst();
        $this->assertEquals(LOG_INFO, $logEntry->getSeverity());
        $this->assertEquals($this->testMessage, $logEntry->getMessage());
        $this->assertEquals(__FUNCTION__, $logEntry->getMethodName());
        $this->assertEquals(__CLASS__, $logEntry->getClassName());
        $this->assertEquals('De.SWebhosting.DatabaseLog', $logEntry->getPackageKey());
    }

    /**
     * Creates a test account and adds it to the database.
     *
     * @return Account
     */
    protected function createTestAccount()
    {
        $testAccount = new Account();
        $testAccount->setAccountIdentifier('testidentifier');
        $testAccount->setAuthenticationProviderName('testprovider');
        $this->accountRepository->add($testAccount);
        return $testAccount;
    }
}
