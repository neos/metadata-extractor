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
 * @see http://www.cipa.jp/std/documents/e/DC-008-Translation-2016-E.pdf data type definitions
 */
class CoordinatesConverter
{
    /**
     * Converts coordinates in DMS (degrees, minutes, seconds) and the cardinal direction reference (E,W,N,S) into
     * DD (decimal degrees) notation.
     *
     * @param array<float> $dmsArray Coordinates in DMS (degrees, minutes, seconds)
     * @param string $cardinalDirectionReference cardinal direction reference (E,W,N,S)
     *
     * @return float Coordinates in DD (decimal degrees)
     */
    public static function convertDmsToDd($dmsArray, $cardinalDirectionReference)
    {
        $degrees = isset($dmsArray[0]) ? $dmsArray[0] : 0.0;
        $minutes = isset($dmsArray[1]) ? $dmsArray[1] : 0.0;
        $seconds = isset($dmsArray[2]) ? $dmsArray[2] : 0.0;

        $flip = ($cardinalDirectionReference === 'W' || $cardinalDirectionReference === 'S') ? -1 : 1;

        return round($flip * ($degrees + $minutes / 60 + $seconds / 3600), 6);
    }
}
