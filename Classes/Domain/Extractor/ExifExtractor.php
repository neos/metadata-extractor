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

use Neos\MetaData\Extractor\Specifications\Exif;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Converter\CoordinatesConverter;
use Neos\MetaData\Extractor\Converter\NumberConverter;
use Neos\MetaData\Extractor\Converter\TimeStampConverter;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\Exception as ResourceException;
use Neos\Flow\ResourceManagement\PersistentResource as FlowResource;

/**
 * @see http://www.cipa.jp/std/documents/e/DC-008-Translation-2016-E.pdf Official standard
 */
class ExifExtractor extends AbstractExtractor
{
    /**
     * @var array
     */
    protected static $compatibleMediaTypes = [
        'image/jpeg',
        'video/jpeg',
        'image/tiff'
    ];

    /**
     * @var array
     */
    protected $exifData = null;

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     *
     * @throws ResourceException
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        $convertedExifData = $this->exifData ?: exif_read_data($resource->createTemporaryLocalCopy(), 'EXIF');

        $deprecatedOrUnmappedProperties = [
            'ISOSpeedRatings' => 'PhotographicSensitivity',
            'GPSVersion' => 'GPSVersionID',
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
            'UndefinedTag:0xA435' => 'LensSerialNumber'
        ];
        foreach ($deprecatedOrUnmappedProperties as $deprecatedOrUnmappedProperty => $newProperty) {
            if (isset($convertedExifData[$deprecatedOrUnmappedProperty])) {
                $convertedExifData[$newProperty] = $convertedExifData[$deprecatedOrUnmappedProperty];
                unset($convertedExifData[$deprecatedOrUnmappedProperty]);
            }
        }

        $rationalProperties = [
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
            'YResolution'
        ];
        foreach ($rationalProperties as $rationalProperty) {
            if (isset($convertedExifData[$rationalProperty])) {
                $convertedExifData[$rationalProperty] = NumberConverter::convertRationalToFloat($convertedExifData[$rationalProperty]);
            }
        }

        $rationalArrayProperties = [
            'GPSTimeStamp',
            'LensSpecification',
            'PrimaryChromaticities',
            'ReferenceBlackWhite',
            'WhitePoint',
            'YCbCrCoefficients',
            'GPSLongitude',
            'GPSLatitude',
            'GPSDestLongitude',
            'GPSDestLatitude'
        ];
        foreach ($rationalArrayProperties as $rationalArrayProperty) {
            if (isset($convertedExifData[$rationalArrayProperty])) {
                foreach ($convertedExifData[$rationalArrayProperty] as $key => $value) {
                    $convertedExifData[$rationalArrayProperty][$key] = NumberConverter::convertRationalToFloat($value);
                }
            }
        }

        if (isset($convertedExifData['GPSVersionID'])) {
            $convertedExifData['GPSVersionID'] = NumberConverter::convertBinaryToVersion($convertedExifData['GPSVersionID']);
        }

        if (isset($convertedExifData['GPSAltitudeRef'], $convertedExifData['GPSAltitude'])) {
            if ($convertedExifData['GPSAltitudeRef'] === 1) {
                $convertedExifData['GPSAltitude'] = -$convertedExifData['GPSAltitude'];
            }
            unset($convertedExifData['GPSAltitudeRef']);
        }

        $gpsProperties = [
            'GPSLongitude',
            'GPSLatitude',
            'GPSDestLongitude',
            'GPSDestLatitude'
        ];
        foreach ($gpsProperties as $gpsProperty) {
            if (isset($convertedExifData[$gpsProperty])) {
                $convertedExifData[$gpsProperty] = CoordinatesConverter::convertDmsToDd($convertedExifData[$gpsProperty], isset($convertedExifData[$gpsProperty . 'Ref']) ? $convertedExifData[$gpsProperty . 'Ref'] : null);
                unset($convertedExifData[$gpsProperty . 'Ref']);
            }
        }

        if (isset($convertedExifData['GPSTimeStamp'], $convertedExifData['GPSDateStamp'])) {
            $convertedExifData['GPSDateTimeStamp'] = TimeStampConverter::combineTimeAndDate($convertedExifData['GPSTimeStamp'], $convertedExifData['GPSDateStamp']);
            unset($convertedExifData['GPSTimeStamp'], $convertedExifData['GPSDateStamp']);
        }

        foreach ($convertedExifData as $property => $value) {
            $convertedExifData[$property] = Exif::interpretValue($property, $value);
        }

        $subSecondProperties = [
            'SubSecTime' => 'DateTime',
            'SubSecTimeOriginal' => 'DateTimeOriginal',
            'SubSecTimeDigitized' => 'DateTimeDigitized'
        ];

        foreach ($subSecondProperties as $subSecondProperty => $dateTimeProperty) {
            if (isset($convertedExifData[$subSecondProperty], $convertedExifData[$dateTimeProperty])) {
                $convertedExifData[$dateTimeProperty] = \DateTime::createFromFormat('Y-m-d H:i:s.u', $convertedExifData[$dateTimeProperty]->format('Y-m-d H:i:s.') . $convertedExifData[$subSecondProperty]);
                unset($convertedExifData[$subSecondProperty]);
            }
        }

        $timeOffsetProperties = [
            'OffsetTime' => 'DateTime',
            'OffsetTimeOriginal' => 'DateTimeOriginal',
            'OffsetTimeDigitized' => 'DateTimeDigitized',
        ];

        foreach ($timeOffsetProperties as $timeOffsetProperty => $dateTimeProperty) {
            if (isset($convertedExifData[$timeOffsetProperty], $convertedExifData[$dateTimeProperty])) {
                $convertedExifData[$dateTimeProperty] = \DateTime::createFromFormat('Y-m-d H:i:s.uP', $convertedExifData[$dateTimeProperty]->format('Y-m-d H:i:s.u') . $convertedExifData[$timeOffsetProperty]);
                unset($convertedExifData[$timeOffsetProperty]);
            }
        }

        // wrongly encoded UserComment breaks saving of the whole data set, so check for correct encoding and remove if necessary
        if (isset($convertedExifData['UserComment'])) {
            $characterCode = substr($convertedExifData['UserComment'], 0, 8);
            $value = substr($convertedExifData['UserComment'], 8);
            switch ($characterCode) {
                case chr(0x41) . chr(0x53) . chr(0x43) . chr(0x49) . chr(0x49) . chr(0x0) . chr(0x0) . chr(0x0): // ASCII
                    $encoding = 'US-ASCII';
                    break;
                case chr(0x4A) . chr(0x49) . chr(0x53) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0): // JIS
                    $encoding = 'EUC-JP';
                    break;
                case chr(0x55) . chr(0x4E) . chr(0x49) . chr(0x43) . chr(0x4F) . chr(0x44) . chr(0x45) . chr(0x0): // Unicode
                    $encoding = 'UTF-8';
                    break;
                default:
                case chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0): // Undefined
                    // try it with ASCII anyway
                    $encoding = 'ASCII';
                    $value = $convertedExifData['UserComment'];
            }
            if (mb_check_encoding($value, $encoding)) {
                $convertedExifData['UserComment'] = $value;
            } else {
                unset($convertedExifData['UserComment']);
            }
        }

        $metaDataCollection->set('exif', new Dto\Exif($convertedExifData));
    }

    /**
     * @param FlowResource $resource
     *
     * @return bool
     */
    public function canHandleExtraction(FlowResource $resource)
    {
        try {
            $this->exifData = exif_read_data($resource->createTemporaryLocalCopy(), 'EXIF');
            return $this->exifData !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
