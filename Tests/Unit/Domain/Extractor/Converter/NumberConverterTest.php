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

use Neos\MetaData\Extractor\Converter\NumberConverter;
use TYPO3\Flow\Tests\UnitTestCase;

/**
 * NumberConverter Test
 */
class NumberConverterTest extends UnitTestCase
{
    /**
     * @return array
     */
    public function rationalDataProvider()
    {
        return [
            'correctRational' => [
                'rational' => '24/1',
                'expected' => 24.0
            ],
            'nonRational' => [
                'rational' => 'twenty-four',
                'expected' => 0.0
            ],
        ];
    }

    /**
     * @test
     * @dataProvider rationalDataProvider
     *
     * @param string $rational
     * @param float $expected
     */
    public function convertRationalToFloat($rational, $expected)
    {
        $actual = NumberConverter::convertRationalToFloat($rational);
        $this->assertEquals($expected, $actual);
    }
}
