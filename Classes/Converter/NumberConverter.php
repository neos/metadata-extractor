<?php

declare(strict_types=1);
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

final class NumberConverter
{
    /**
     * Converts a rational string like EXIF / (S)RATIONAL into a float number.
     */
    public static function convertRationalToFloat(string|int|float $rationalString) : float
    {
        $rationalString = (string)$rationalString;
        if (\preg_match('#^(-?\d+)\/(\d+)$#', $rationalString, $matches)) {
            $divisor = (float)$matches[2];
            if ($divisor !== 0.0) {
                return (int)$matches[1] / $divisor;
            }
        }

        return 0.0;
    }

    /**
     * Converts a version in the format like 0x02020000 to 2.2.0.0
     */
    public static function convertBinaryToVersion(string $binaryVersion) : string
    {
        $versionParts = \str_split(\bin2hex($binaryVersion), 2);
        $versionParts = \array_map('\intval', $versionParts);

        return \implode('.', $versionParts);
    }
}
