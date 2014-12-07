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

use TYPO3\Flow\Annotations as Flow;

/**
 * Repository for log entries
 *
 * @Flow\Scope("singleton")
 */
class LogEntryRepository extends \TYPO3\Flow\Persistence\Repository {

	/**
	 * Finds a log entry by user and message. The latest entries are on the top and
	 * no limit is applied by default.
	 *
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user the user to search for
	 * @param string $message the message to search for
	 * @param array $orderings the order of the log messages
	 * @param int $limit limit the number of results
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface
	 */
	public function findByUserAndMessage($user, $message, $limit = NULL, $orderings = array('dateTime' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING)) {

		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$query->equals('userObjectIdentifier', $user),
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
	 * Removes all log entries from the given user
	 *
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $user
	 */
	public function removeAllFromUser($user) {
		$userLogEntries = $this->findByUser($user);
		foreach ($userLogEntries as $logEntry) {
			$this->remove($logEntry);
		}
	}
}