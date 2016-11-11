<?php
namespace Neos\MetaData\Extractor\Tests\Unit\Converter;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\MetaData\Extractor\Converter\TimeStampConverter;
use TYPO3\Flow\Tests\UnitTestCase;

/**
 * TimeStampConverter Test
 */
class TimeStampConverterTest extends UnitTestCase
{
    /**
     * @return array
     */
    public function timeStampDataProvider()
    {
        return [
            [
                'timeStamp' => [
                    11.0,
                    16.0,
                    53.0
                ],
                'dateStamp' => '2016:02:05',
                'expected' => \DateTime::createFromFormat('YmdHis', '20160205111653')
            ]
        ];
    }

    /**
     * @test
     * @dataProvider timeStampDataProvider
     *
     * @param array $timeStamp
     * @param string $dateStamp
     * @param \DateTime $expected
     */
    public function combineTimeAndDate($timeStamp, $dateStamp, $expected)
    {
        $actual = TimeStampConverter::combineTimeAndDate($timeStamp, $dateStamp);
        $this->assertEquals($expected, $actual);
    }
}
