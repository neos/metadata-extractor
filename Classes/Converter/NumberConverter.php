<?php
namespace Neos\MetaData\Extractor\Converter;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

class NumberConverter
{
    /**
     * Converts a rational string like EXIF / (S)RATIONAL into a float number.
     *
     * @param string $rationalString
     *
     * @return float
     */
    public static function convertRationalToFloat($rationalString)
    {
        if (preg_match('#^(-?\d+)/(\d+)$#', $rationalString, $matches)) {
            $divisor = (float)$matches[2];
            if ($divisor !== 0.0) {
                return (int)$matches[1] / $divisor;
            }
        }

        return 0.0;
    }
}
