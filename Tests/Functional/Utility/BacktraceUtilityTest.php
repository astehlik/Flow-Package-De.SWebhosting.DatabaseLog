<?php
declare(strict_types=1);

namespace De\SWebhosting\DatabaseLog\Tests\Functional\Utility;

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

use De\SWebhosting\DatabaseLog\Utility\BacktraceUtility;
use Neos\Flow\Tests\FunctionalTestCase;

/**
 * Tests for the BacktraceUtility class.
 */
class BacktraceUtilityTest extends FunctionalTestCase
{
    /**
     * @var \De\SWebhosting\DatabaseLog\Utility\BacktraceUtility
     */
    protected $backtraceUtility;

    /**
     * Initializes the action logger.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->backtraceUtility = $this->objectManager->get(BacktraceUtility::class);
    }

    /**
     * @dataProvider backtraceDataIsDeterminedCorrectlyDataProvider
     * @test
     * @param int $offset
     * @param array $expectedResult
     */
    public function backtraceDataIsDeterminedCorrectly($offset, $expectedResult)
    {
        $result = $this->backtraceUtility->getBacktraceData($offset);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function backtraceDataIsDeterminedCorrectlyDataProvider()
    {
        return [
            'inner method' => [
                0,
                [
                    'De.SWebhosting.DatabaseLog',
                    'De\SWebhosting\DatabaseLog\Utility\BacktraceUtility',
                    'getBacktraceData',
                ],
            ],
            'test method' => [
                1,
                [
                    'De.SWebhosting.DatabaseLog',
                    'De\SWebhosting\DatabaseLog\Tests\Functional\Utility\BacktraceUtilityTest',
                    'backtraceDataIsDeterminedCorrectly',
                ],
            ],
        ];
    }
}
