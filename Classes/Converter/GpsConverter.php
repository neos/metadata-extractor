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

/*
 * For data type definitions see http://www.cipa.jp/std/documents/e/DC-008-2012_E.pdf
 */
class GpsConverter
{

    /**
     * Converts the GPS-3-rational data and reference
     * into a WGS84 float number.
     *
     * @param array $gpsRationalArray
     * @param string $gpsReference
     * @return float
     */
    public static function convertRationalArrayAndReferenceToFloat(array $gpsRationalArray, $gpsReference)
    {
        $degrees = isset($gpsRationalArray[0]) ? NumberConverter::convertRationalToFloat($gpsRationalArray[0]) : 0.0;
        $minutes = isset($gpsRationalArray[1]) ? NumberConverter::convertRationalToFloat($gpsRationalArray[1]) : 0.0;
        $seconds = isset($gpsRationalArray[2]) ? NumberConverter::convertRationalToFloat($gpsRationalArray[2]) : 0.0;

        $flip = ($gpsReference == 'W' or $gpsReference == 'S') ? -1 : 1;

        return round($flip * ($degrees + $minutes / 60 + $seconds / 3600), 6);
    }
}