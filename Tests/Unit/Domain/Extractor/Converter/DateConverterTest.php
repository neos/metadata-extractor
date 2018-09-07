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

use Neos\Flow\Tests\UnitTestCase;
use Neos\MetaData\Extractor\Converter\DateConverter;

class DateConverterTest extends UnitTestCase
{
    /**
     * @return mixed[][]
     */
    public function gpsTimeStampDataProvider() : array
    {
        return [
            [
                'dateStamp' => '2016:02:05',
                'timeStamp' => [
                    11.0,
                    16.0,
                    53.0,
                ],
                'expected' => \DateTime::createFromFormat('YmdHis', '20160205111653'),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider gpsTimeStampDataProvider
     *
     * @param string $dateStamp
     * @param string[] $timeStamp
     * @param \DateTime $expected
     * @return void
     */
    public function convertGpsDateAndTime(string $dateStamp, array $timeStamp, \DateTime $expected)
    {
        $actual = DateConverter::convertGpsDateAndTime($dateStamp, $timeStamp);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return mixed[][]
     */
    public function iso8601DateAndTimeDataProvider() : array
    {
        return [
            'dateStringOnly' => [
                'dateString' => '20130918',
                'timeString' => '',
                'expected' => \DateTime::createFromFormat('YmdHisO', '20130918000000+0000'),
            ],
            'dateAndTimeAndUtcDifference' => [
                'dateString' => '20130918',
                'timeString' => '105911+0200',
                'expected' => \DateTime::createFromFormat('YmdHisO', '20130918105911+0200'),
            ],
            'dateAndTimeWithoutSeparator' => [
                'dateString' => '20130918',
                'timeString' => '105911',
                'expected' => \DateTime::createFromFormat('YmdHisO', '20130918105911+0000'),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider iso8601DateAndTimeDataProvider
     *
     * @param string $dateString
     * @param string|null $timeString
     * @param \DateTime $expected
     * @return void
     */
    public function convertIso8601DateAndTimeString(string $dateString, string $timeString, \DateTime $expected)
    {
        $actual = DateConverter::convertIso8601DateAndTimeString($dateString, $timeString);
        $this->assertInstanceOf(\DateTime::class, $actual, 'The date and time could not be converted');
        $this->assertEquals($expected, $actual);
    }
}
