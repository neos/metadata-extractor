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
        if (preg_match('#^(-?\d+)\/(\d+)$#', $rationalString, $matches)) {
            $divisor = (float)$matches[2];
            if ($divisor !== 0.0) {
                return (int)$matches[1] / $divisor;
            }
        }

        return 0.0;
    }

    /**
     * Converts a version in the format like 0x02020000
     * to 2.2.0.0
     *
     * @param string $binaryVersion
     * @return string
     */
    public static function convertBinaryToVersion($binaryVersion)
    {
        $versionParts = str_split((string)bin2hex($binaryVersion), 2);
        $versionParts = array_map('intval', $versionParts);
        return implode('.', $versionParts);
    }
}
