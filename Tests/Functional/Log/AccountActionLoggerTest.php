<?php
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
use De\SWebhosting\DatabaseLog\Log\AccountActionLoggerInterface;
use Neos\Flow\Security\AccountRepository;

/**
 * Tests for the AccountActionLogger class.
 */
class AccountActionLoggerTest extends \Neos\Flow\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @var AccountActionLoggerInterface
	 */
	protected $accountActionLogger;

	/**
	 * @var AccountRepository
	 */
	protected $accountRepository;

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
	public function setUp() {

		parent::setUp();

		if (!$this->persistenceManager instanceof \Neos\Flow\Persistence\Doctrine\PersistenceManager) {
			$this->markTestSkipped('Doctrine persistence is not enabled');
		}

		$this->accountRepository = $this->objectManager->get(AccountRepository::class);
		$this->logEntryRepository = $this->objectManager->get(LogEntryRepository::class);
		$this->accountActionLogger = $this->objectManager->get(AccountActionLoggerInterface::class);
	}

	/**
	 * @test
	 */
	public function accountIsStoredAsExcpected() {
		$testAccount = $this->createTestAccount();
		$this->accountActionLogger->logAccountAction($this->testMessage, $testAccount);
		$this->persistenceManager->persistAll();
		$entries = $this->logEntryRepository->findAll();

		$this->assertEquals(1, $entries->count());

		/** @var LogEntry $logEntry */
		$logEntry = $entries->getFirst();
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
	public function logDataIsStoredAsExpected() {

		$this->accountActionLogger->log($this->testMessage);
		$this->persistenceManager->persistAll();
		$entries = $this->logEntryRepository->findAll();
		$this->assertEquals(1, $entries->count());

		/** @var LogEntry $logEntry */
		$logEntry = $entries->getFirst();
		$this->assertEquals($this->testMessage, $logEntry->getMessage());
		$this->assertEquals(__FUNCTION__, $logEntry->getMethodName());
		$this->assertEquals(__CLASS__, $logEntry->getClassName());

		// TODO: This is currently the invalid behavior of TYPO3 Flow.
		$this->assertEquals('SWebhosting', $logEntry->getPackageKey());
	}

	/**
	 * Creates a test account and adds it to the database.
	 *
	 * @return \Neos\Flow\Security\Account
	 */
	protected function createTestAccount() {
		$testAccount = new \Neos\Flow\Security\Account();
		$testAccount->setAccountIdentifier('testidentifier');
		$testAccount->setAuthenticationProviderName('testprovider');
		$this->accountRepository->add($testAccount);
		return $testAccount;
	}
}