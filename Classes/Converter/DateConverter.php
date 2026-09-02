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

final class DateConverter
{
    /**
     * Combines the EXIF GPSTimeStamp and GPSDateStamp into a DateTime object
     *
     * @param string[] $gpsTimeStamp
     */
    public static function convertGpsDateAndTime(string $gpsDateStamp, array $gpsTimeStamp) : \DateTime
    {
        $dateTime = \DateTime::createFromFormat(
            'Y:m:d H:i:s',
            $gpsDateStamp . ' ' . \sprintf('%02d:%02d:%02d', (int)$gpsTimeStamp[0], (int)$gpsTimeStamp[1], (int)$gpsTimeStamp[2])
        );

        if ($dateTime === false) {
            throw new \InvalidArgumentException('Could not create DateTime from GPS date and time.', 1788349233);
        }

        return $dateTime;
    }

    /**
     * Combines ISO 8601 like date and time string into a DateTime Object
     */
    public static function convertIso8601DateAndTimeString(string $dateString, ?string $timeString = null): \DateTime|false
    {
        if (empty($timeString)) {
            $timeString = '000000+0000';
        } elseif (\strpos($timeString, '+') === false && \strpos($timeString, '-') === false) {
            $timeString .= '+0000';
        }

        return \DateTime::createFromFormat('YmdHisO', $dateString . $timeString);
    }
}
