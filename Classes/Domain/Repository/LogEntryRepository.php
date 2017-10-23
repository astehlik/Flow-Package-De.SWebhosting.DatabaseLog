<?php
namespace De\SWebhosting\DatabaseLog\Domain\Repository;

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

use Neos\Flow\Annotations as Flow;

/**
 * Repository for log entries.
 *
 * @Flow\Scope("singleton")
 * @method \Neos\Flow\Persistence\QueryResultInterface findByUserObjectIdentifier(string $user)
 */
class LogEntryRepository extends \Neos\Flow\Persistence\Repository {

	/**
	 * Finds a log entry by user and message. The latest entries are on the top and no limit is applied by default.
	 *
	 * @param string|\TYPO3\Party\Domain\Model\AbstractParty $userObjectIdentifier The user or the object identifier of the user to search for.
	 * @param string $message The message to search for.
	 * @param array $orderings The order of the log messages.
	 * @param int $limit Limit the number of results.
	 * @return \Neos\Flow\Persistence\QueryResultInterface
	 */
	public function findByUserAndMessage($userObjectIdentifier, $message, $limit = NULL, $orderings = array('dateTime' => \Neos\Flow\Persistence\QueryInterface::ORDER_DESCENDING)) {

		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$query->equals('userObjectIdentifier', $userObjectIdentifier),
				$query->equals('message', $message)
			)
		);

		$query->setOrderings($orderings);

		if (isset($limit)) {
			$query->setLimit($limit);
		}

		return $query->execute();
	}

	/**
	 * Removes all log entries from the given user.
	 *
	 * @param string|\TYPO3\Party\Domain\Model\AbstractParty $userObjectIdentifier
	 */
	public function removeAllFromUser($userObjectIdentifier) {
		$userLogEntries = $this->findByUserObjectIdentifier($userObjectIdentifier);
		foreach ($userLogEntries as $logEntry) {
			$this->remove($logEntry);
		}
	}
}