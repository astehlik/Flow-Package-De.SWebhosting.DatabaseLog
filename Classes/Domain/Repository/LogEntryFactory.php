<?php
declare(strict_types=1);

namespace De\SWebhosting\DatabaseLog\Domain\Repository;

use De\SWebhosting\DatabaseLog\Domain\Model\LogEntry;
use InvalidArgumentException;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Security\Account;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Party\Domain\Model\Person;
use Neos\Party\Domain\Service\PartyService;

class LogEntryFactory
{
    /**
     * @var Bootstrap
     */
    private $bootstrap;

    /**
     * @var bool
     */
    private $enableIpAdressLogging;

    /**
     * @var string
     */
    private $logEntryAccountIdentifier = '';

    /**
     * @var array|null
     */
    private $logEntryAdditionalData = null;

    /**
     * @var string
     */
    private $logEntryAuthenticationProviderName = '';

    /**
     * @var string|null
     */
    private $logEntryClassName = null;

    /**
     * @var string|null
     */
    private $logEntryIpAddress = null;

    /**
     * @var string
     */
    private $logEntryMessage;

    /**
     * @var string|null
     */
    private $logEntryMethodName = null;

    /**
     * @var string|null
     */
    private $logEntryPackageKey = null;

    /**
     * @var int
     */
    private $logEntrySeverity;

    /**
     * @var string|null
     */
    private $logEntryUserFullName = null;

    /**
     * @var string|null
     */
    private $logEntryUserObjectIdentifier = null;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PersistenceManagerInterface
     */
    private $persistenceManager;

    public function __construct(
        string $message,
        int $severity,
        Bootstrap $bootstrap,
        ObjectManagerInterface $objectManager,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->logEntryMessage = $message;
        $this->logEntrySeverity = $severity;

        $this->bootstrap = $bootstrap;
        $this->objectManager = $objectManager;
        $this->persistenceManager = $persistenceManager;
    }

    public function createLogEntry(): LogEntry
    {
        $this->initializeIpAddress();

        return new LogEntry(
            $this->logEntryMessage,
            $this->logEntrySeverity,
            $this->logEntryAdditionalData,
            $this->logEntryPackageKey,
            $this->logEntryClassName,
            $this->logEntryMethodName,
            $this->logEntryIpAddress,
            $this->logEntryAccountIdentifier,
            $this->logEntryAuthenticationProviderName,
            $this->logEntryUserObjectIdentifier,
            $this->logEntryUserFullName
        );
    }

    public function enableIpAdressLogging(bool $shouldLogIps): void
    {
        $this->enableIpAdressLogging = $shouldLogIps;
    }

    public function logAdditionalData($additionalData)
    {
        if (!isset($additionalData)) {
            return;
        }

        if (!is_array($additionalData)) {
            throw new InvalidArgumentException(
                'For the database backend the additional data must be an array'
            );
        }

        if (array_key_exists('De.SWebhosting.DatabaseLog.Account', $additionalData)) {
            $possibleAccount = $additionalData['De.SWebhosting.DatabaseLog.Account'];
            unset($additionalData['De.SWebhosting.DatabaseLog.Account']);

            if ($possibleAccount instanceof Account) {
                $this->initializeAccount($possibleAccount);
            }
        }

        if (count($additionalData)) {
            $this->logEntryAdditionalData = $additionalData;
        }
    }

    public function logCodeLocation(?string $packageKey, ?string $className, ?string $methodName): void
    {
        $this->logEntryPackageKey = $packageKey;
        $this->logEntryClassName = $className;
        $this->logEntryMethodName = $methodName;
    }

    /**
     * Sets the account properties and if the account has a related user it will
     * also set the user properties when the the party package is available.
     *
     * @param Account $account
     */
    private function initializeAccount(Account $account): void
    {
        $this->logEntryAccountIdentifier = (string)$account->getAccountIdentifier();
        $this->logEntryAuthenticationProviderName = (string)$account->getAuthenticationProviderName();

        if (!class_exists('Neos\\Party\\Domain\\Service\\PartyService')) {
            return;
        }

        /** @var PartyService $partyService */
        $partyService = $this->objectManager->get(PartyService::class);
        $party = $partyService->getAssignedPartyOfAccount($account);
        if (isset($party)) {
            $this->initializeUser($party);
        }
    }

    private function initializeIpAddress(): void
    {
        if (!$this->enableIpAdressLogging) {
            return;
        }

        $requestHandler = $this->bootstrap->getActiveRequestHandler();
        if (!$requestHandler instanceof HttpRequestHandlerInterface) {
            return;
        }

        $request = $requestHandler->getComponentContext()->getHttpRequest();
        $serverParams = $request->getServerParams();
        $remoteAddress = (string)($serverParams['REMOTE_ADDR'] ?? '');

        if ($remoteAddress === '') {
            return;
        }

        $this->logEntryIpAddress = $remoteAddress;
    }

    /**
     * If the user is not NULL the userObjectIdentifier property will be set
     * to the object identifier of the given user. Additionally setUserFullName
     * will be called.
     *
     * @param AbstractParty $user
     */
    private function initializeUser(AbstractParty $user): void
    {
        $this->logEntryUserObjectIdentifier = $this->persistenceManager->getIdentifierByObject($user);
        $this->initializeUserFullName($user);
    }

    /**
     * Sets the userFullName property if the given user is an instance
     * of Person and has an associated name. Otherwise the property
     * is reset to NULL.
     *
     * @param AbstractParty $user
     */
    private function initializeUserFullName(AbstractParty $user): void
    {
        if (!$user instanceof Person) {
            return;
        }

        $personName = $user->getName();
        if (!isset($personName)) {
            return;
        }

        $this->logEntryUserFullName = $personName->getFullName();
    }
}
