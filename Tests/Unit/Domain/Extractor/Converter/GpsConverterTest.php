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

use Neos\MetaData\Extractor\Converter\GpsConverter;
use TYPO3\Flow\Tests\UnitTestCase;

/**
 * GpsConverter Test
 */
class GpsConverterTest extends UnitTestCase
{
    /**
     * @return array
     */
    public function gpsDataProvider()
    {
        return [
            'latitude' => [
                'gpsRationalArray' => [
                    '46/1',
                    '395872/10000',
                    '0/1'
                ],
                'gpsReference' => 'S',
                'expected' => -46.659787000000001
            ],
            'longitude' => [
                'gpsRationalArray' => [
                    '168/1',
                    '508218/10000',
                    '0/1'
                ],
                'gpsReference' => 'E',
                'expected' => 168.84702999999999
            ],
        ];
    }

    /**
     * @test
     * @dataProvider gpsDataProvider
     *
     * @param array $gpsRationalArray
     * @param string $gpsReference
     * @param float $expected
     */
    public function convertRationalArrayAndReferenceToFloat($gpsRationalArray, $gpsReference, $expected)
    {
        $actual = GpsConverter::convertRationalArrayAndReferenceToFloat($gpsRationalArray, $gpsReference);
        $this->assertEquals($expected, $actual);
    }
}
