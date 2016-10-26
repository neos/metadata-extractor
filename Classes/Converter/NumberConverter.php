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

/**
 * Number Converter
 */
class NumberConverter
{
    /**
     * Converts a rational string like EXIF / RATIONAL into a float number.
     *
     * @param string $rationalString
     *
     * @return float
     */
    public static function convertRationalToFloat($rationalString)
    {
        if (preg_match('#^(\d+)/(\d+)$#', $rationalString, $matches)) {
            return (int)$matches[1] / (float)$matches[2];
        }

        return 0.0;
    }
}
