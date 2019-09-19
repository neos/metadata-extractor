<?php
namespace Neos\MetaData\Extractor\Domain\Extractor;

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
use Neos\Flow\ResourceManagement\Exception as FlowResourceException;
use Neos\Flow\ResourceManagement\PersistentResource as FlowResource;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Converter\CoordinatesConverter;
use Neos\MetaData\Extractor\Converter\DateConverter;
use Neos\MetaData\Extractor\Converter\NumberConverter;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\Extractor\Specifications\Exif;

/**
 * @Flow\Scope("singleton")
 * @see http://www.cipa.jp/std/documents/e/DC-008-Translation-2016-E.pdf Official standard
 */
class ExifExtractor extends AbstractExtractor
{
    /**
     * @var string[]
     */
    protected static $compatibleMediaTypes = [
        'image/jpeg',
        'image/tiff',
        'video/jpeg',
    ];

    /**
     * @var string[]
     */
    protected static $deprecatedOrUnmappedProperties = [
        'GPSVersion' => 'GPSVersionID',
        'ISOSpeedRatings' => 'PhotographicSensitivity',
        'UndefinedTag:0x8830' => 'SensitivityType',
        'UndefinedTag:0x8832' => 'RecommendedExposureIndex',
        'UndefinedTag:0x9010' => 'OffsetTime',
        'UndefinedTag:0x9011' => 'OffsetTimeOriginal',
        'UndefinedTag:0x9012' => 'OffsetTimeDigitized',
        'UndefinedTag:0x9400' => 'Temperature',
        'UndefinedTag:0x9401' => 'Humidity',
        'UndefinedTag:0x9402' => 'Pressure',
        'UndefinedTag:0x9403' => 'WaterDepth',
        'UndefinedTag:0x9404' => 'Acceleration',
        'UndefinedTag:0x9405' => 'CameraElevationAngle',
        'UndefinedTag:0xA430' => 'CameraOwnerName',
        'UndefinedTag:0xA431' => 'BodySerialNumber',
        'UndefinedTag:0xA432' => 'LensSpecification',
        'UndefinedTag:0xA433' => 'LensMake',
        'UndefinedTag:0xA434' => 'LensModel',
        'UndefinedTag:0xA435' => 'LensSerialNumber',
        'UndefinedTag:0xA500' => 'Gamma',
    ];

    /**
     * @var string[]
     */
    protected static $rationalProperties = [
        'Acceleration',
        'ApertureValue',
        'BrightnessValue',
        'CameraElevationAngle',
        'CompressedBitsPerPixel',
        'DigitalZoomRatio',
        'ExposureBiasValue',
        'ExposureIndex',
        'ExposureTime',
        'FlashEnergy',
        'FNumber',
        'FocalLength',
        'FocalPlaneXResolution',
        'FocalPlaneYResolution',
        'GainControl',
        'Gamma',
        'GPSAltitude',
        'GPSDestBearing',
        'GPSDestDistance',
        'GPSDOP',
        'GPSHPositioningError',
        'GPSImgDirection',
        'GPSSpeed',
        'GPSTrack',
        'Humidity',
        'MaxApertureValue',
        'Pressure',
        'ShutterSpeedValue',
        'SubjectDistance',
        'Temperature',
        'WaterDepth',
        'XResolution',
        'YResolution',
    ];

    /**
     * @var string[]
     */
    protected static $rationalArrayProperties = [
        'GPSDestLatitude',
        'GPSDestLongitude',
        'GPSLatitude',
        'GPSLongitude',
        'GPSTimeStamp',
        'LensSpecification',
        'PrimaryChromaticities',
        'ReferenceBlackWhite',
        'WhitePoint',
        'YCbCrCoefficients',
    ];

    /**
     * @var string[]
     */
    protected static $gpsProperties = [
        'GPSDestLatitude',
        'GPSDestLongitude',
        'GPSLatitude',
        'GPSLongitude',
    ];

    /**
     * @var string[]
     */
    protected static $subSecondProperties = [
        'SubSecTime' => 'DateTime',
        'SubSecTimeDigitized' => 'DateTimeDigitized',
        'SubSecTimeOriginal' => 'DateTimeOriginal',
    ];

    /**
     * @var string[]
     */
    protected static $timeOffsetProperties = [
        'OffsetTime' => 'DateTime',
        'OffsetTimeDigitized' => 'DateTimeDigitized',
        'OffsetTimeOriginal' => 'DateTimeOriginal',
    ];

    /**
     * @inheritdoc
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        $temporaryLocalCopyPath = $resource->createTemporaryLocalCopy();
        try {
            $exifData = @\exif_read_data($temporaryLocalCopyPath, 'EXIF');
        } catch (FlowResourceException $exception) {
            throw new ExtractorException(
                'Could not extract EXIF data from ' . $resource->getFilename(),
                1484059228,
                $exception
            );
        } finally {
            unlink($temporaryLocalCopyPath);
        }

        if ($exifData === false) {
            throw new ExtractorException('Could not extract EXIF data from ' . $resource->getFilename(), 1484056779);
        }

        foreach (static::$deprecatedOrUnmappedProperties as $deprecatedOrUnmappedProperty => $newProperty) {
            if (isset($exifData[$deprecatedOrUnmappedProperty])) {
                $exifData[$newProperty] = $exifData[$deprecatedOrUnmappedProperty];
                unset($exifData[$deprecatedOrUnmappedProperty]);
            }
        }

        foreach (static::$rationalProperties as $rationalProperty) {
            if (isset($exifData[$rationalProperty])) {
                $exifData[$rationalProperty] = NumberConverter::convertRationalToFloat($exifData[$rationalProperty]);
            }
        }

        foreach (static::$rationalArrayProperties as $rationalArrayProperty) {
            if (isset($exifData[$rationalArrayProperty])) {
                // Although defined as an array, some implementations only use one rational
                if (\is_array($exifData[$rationalArrayProperty])) {
                    foreach ($exifData[$rationalArrayProperty] as $key => $value) {
                        $exifData[$rationalArrayProperty][$key] = NumberConverter::convertRationalToFloat($value);
                    }
                } else {
                    $exifData[$rationalArrayProperty] = NumberConverter::convertRationalToFloat(
                        $exifData[$rationalArrayProperty]
                    );
                }
            }
        }

        if (isset($exifData['PhotographicSensitivity']) && \is_array($exifData['PhotographicSensitivity'])) {
            $exifData['PhotographicSensitivity'] = (int)\current($exifData['PhotographicSensitivity']);
        }

        if (isset($exifData['GPSVersionID'])) {
            $exifData['GPSVersionID'] = NumberConverter::convertBinaryToVersion($exifData['GPSVersionID']);
        }

        if (isset($exifData['GPSAltitudeRef'], $exifData['GPSAltitude'])) {
            if ($exifData['GPSAltitudeRef'] === 1) {
                $exifData['GPSAltitude'] = -$exifData['GPSAltitude'];
            }
            unset($exifData['GPSAltitudeRef']);
        }

        foreach (static::$gpsProperties as $gpsProperty) {
            if (isset($exifData[$gpsProperty])) {
                $exifData[$gpsProperty] = CoordinatesConverter::convertDmsToDd(
                    $exifData[$gpsProperty],
                    $exifData[$gpsProperty . 'Ref'] ?? null
                );
                unset($exifData[$gpsProperty . 'Ref']);
            }
        }

        if (isset($exifData['GPSTimeStamp'], $exifData['GPSDateStamp'])) {
            $exifData['GPSDateTimeStamp'] = DateConverter::convertGpsDateAndTime(
                $exifData['GPSDateStamp'],
                $exifData['GPSTimeStamp']
            );
            unset($exifData['GPSTimeStamp'], $exifData['GPSDateStamp']);
        }

        foreach ($exifData as $property => $value) {
            $exifData[$property] = Exif::interpretValue($property, $value);
        }

        foreach (static::$subSecondProperties as $subSecondProperty => $dateTimeProperty) {
            if (isset($exifData[$subSecondProperty], $exifData[$dateTimeProperty])) {
                $exifData[$dateTimeProperty] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s.u',
                    $exifData[$dateTimeProperty]->format('Y-m-d H:i:s.') . $exifData[$subSecondProperty]
                );
                unset($exifData[$subSecondProperty]);
            }
        }

        foreach (static::$timeOffsetProperties as $timeOffsetProperty => $dateTimeProperty) {
            if (isset($exifData[$timeOffsetProperty], $exifData[$dateTimeProperty])) {
                $exifData[$dateTimeProperty] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s.uP',
                    $exifData[$dateTimeProperty]->format('Y-m-d H:i:s.u') . $exifData[$timeOffsetProperty]
                );
                unset($exifData[$timeOffsetProperty]);
            }
        }

        // wrongly encoded UserComment breaks saving of the whole data set,
        // so check for correct encoding and remove if necessary
        if (isset($exifData['UserComment'])) {
            $characterCode = \substr($exifData['UserComment'], 0, 8);
            $value = \substr($exifData['UserComment'], 8);
            switch ($characterCode) {
                case \chr(0x41) . \chr(0x53) . \chr(0x43) . \chr(0x49) . \chr(0x49)
                    . \chr(0x0) . \chr(0x0) . \chr(0x0): // ASCII
                    $encoding = 'US-ASCII';
                    break;
                case \chr(0x4A) . \chr(0x49) . \chr(0x53) . \chr(0x0) . \chr(0x0)
                    . \chr(0x0) . \chr(0x0) . \chr(0x0): // JIS
                    $encoding = 'EUC-JP';
                    break;
                case \chr(0x55) . \chr(0x4E) . \chr(0x49) . \chr(0x43) . \chr(0x4F)
                    . \chr(0x44) . \chr(0x45) . \chr(0x0): // Unicode
                    $encoding = 'UTF-8';
                    break;
                case \chr(0x0) . \chr(0x0) . \chr(0x0) . \chr(0x0) . \chr(0x0)
                    . \chr(0x0) . \chr(0x0) . \chr(0x0): // Undefined
                default:
                    // try it with ASCII anyway
                    $encoding = 'ASCII';
                    $value = $exifData['UserComment'];
            }
            if (\mb_check_encoding($value, $encoding)) {
                $exifData['UserComment'] = $value;
            } else {
                unset($exifData['UserComment']);
            }
        }

        $metaDataCollection->set('exif', new Dto\Exif($exifData));
    }
}
