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

class DateConverter
{
    /**
     * Combines the EXIF GPSTimeStamp and GPSDateStamp into a DateTime object
     *
     * @param string $gpsDateStamp
     * @param string[] $gpsTimeStamp
     * @return \DateTime
     */
    public static function convertGpsDateAndTime(string $gpsDateStamp, array $gpsTimeStamp) : \DateTime
    {
        return \DateTime::createFromFormat(
            'Y:m:d H:i:s',
            $gpsDateStamp . ' ' . \sprintf('%02d:%02d:%02d', (int)$gpsTimeStamp[0], (int)$gpsTimeStamp[1], (int)$gpsTimeStamp[2])
        );
    }

    /**
     * Combines ISO 8601 like date and time string into a DateTime Object
     *
     * @param string $dateString
     * @param string|null $timeString
     * @return \DateTime|bool
     */
    public static function convertIso8601DateAndTimeString(string $dateString, string $timeString = null)
    {
        if (empty($timeString)) {
            $timeString = '000000+0000';
        } elseif (\strpos($timeString, '+') === false && \strpos($timeString, '-') === false) {
            $timeString .= '+0000';
        }

        return \DateTime::createFromFormat('YmdHisO', $dateString . $timeString);
    }
}
