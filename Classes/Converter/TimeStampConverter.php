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

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class TimeStampConverter
{
    /**
     * Combines the EXIF GPSTimeStamp and GPSDateStamp into a DateTime object
     *
     * @param array $gpsTimeStamp
     * @param string $gpsDateStamp
     *
     * @return \DateTime
     */
    public static function combineTimeAndDate($gpsTimeStamp, $gpsDateStamp)
    {
        return \DateTime::createFromFormat('Y:m:d H:i:s', $gpsDateStamp . ' ' . (int)$gpsTimeStamp[0] . ':' . (int)$gpsTimeStamp[1] . ':' . (int)$gpsTimeStamp[2]);
    }
}
