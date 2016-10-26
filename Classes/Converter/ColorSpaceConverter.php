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
 * Color Space Converter
 */
class ColorSpaceConverter
{
    /**
     * ColorSpace Identifier according to
     * http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html
     *
     * @var array
     */
    protected static $colorSpaceIdentifierMap = [
        1 => 'sRGB',
        2 => 'Adobe RGB',
        0xfffd => 'Wide Gamut RGB',
        0xfffe => 'ICC Profile',
        0xffff => 'Uncalibrated',
        5 => 'CMYK',
        6 => 'YUV'
    ];

    /**
     * @param $colorSpaceId
     *
     * @return string
     */
    public static function translateColorSpaceId($colorSpaceId)
    {
        $colorSpaceId = (int)$colorSpaceId;
        if (isset(static::$colorSpaceIdentifierMap[$colorSpaceId])) {
            return static::$colorSpaceIdentifierMap[$colorSpaceId];
        }

        return 'Unknown';
    }
}
