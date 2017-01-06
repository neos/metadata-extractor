<?php
namespace Neos\MetaData\Extractor\Tests\Functional\Domain\Extractor;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\Extractor\ExifExtractor;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTest;

class ExifExtractorTest extends AbstractExtractorTest
{
    /**
     * @var ExifExtractor
     */
    protected $exifExtractor;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->exifExtractor = new ExifExtractor();
    }

    /**
     * @test
     */
    public function extractMetaData()
    {
        $metaDataCollection = new MetaDataCollection();


        $this->exifExtractor->extractMetaData($this->testAsset->getResource(), $metaDataCollection);

        $exifDto = $metaDataCollection->get('exif');

        $this->assertInstanceOf(Dto\Exif::class, $exifDto);

        $expectedExifData = [
            'ApertureValue' => 6.0,
            'Artist' => 'Daniel Lienert',
            'BodySerialNumber' => '1330801819',
            'CameraOwnerName' => 'Daniel Lienert',
            'ColorSpace' => 'sRGB',
            'Copyright' => '© Daniel Lienert',
            'CustomRendered' => 'Normal process',
            'DateTime' => \DateTime::createFromFormat('YmdHis', '20160407070947'),
            'DateTimeDigitized' => \DateTime::createFromFormat('YmdHisu', '20130917235911260000'),
            'DateTimeOriginal' => \DateTime::createFromFormat('YmdHis', '20130918105911'),
            'ExifVersion' => '0230',
            'ExposureBiasValue' => 0.0,
            'ExposureMode' => 'Manual exposure',
            'ExposureProgram' => 'Manual',
            'ExposureTime' => 0.0015625,
            'Flash' => 'Flash did not fire. No strobe return detection function. Compulsory flash suppression. Flash function present. No red-eye reduction mode or unknown.',
            'FNumber' => 8.0,
            'FocalLength' => 24,
            'FocalPlaneResolutionUnit' => 'inches',
            'FocalPlaneXResolution' => 3849.2117888965,
            'FocalPlaneYResolution' => 3908.1419624217,
            'GPSAltitude' => 17.8953,
            'GPSImgDirection' => 180.0,
            'GPSLatitude' => -46.659787,
            'GPSLongitude' => 168.84703,
            'ImageDescription' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'LensModel' => 'EF24-105mm f/4L IS USM',
            'LensSpecification' => [24.0, 105.0, 0.0, 0.0],
            'Make' => 'Canon',
            'MaxApertureValue' => 4.0,
            'MeteringMode' => 'Pattern',
            'Model' => 'Canon EOS 5D Mark II',
            'PhotographicSensitivity' => 100,
            'ResolutionUnit' => 'inches',
            'SceneCaptureType' => 'Standard',
            'ShutterSpeedValue' => 9.321928,
            'Software' => 'Adobe Photoshop Lightroom 6.3 (Macintosh)',
            'UserComment' => 'Great weather',
            'WhiteBalance' => 'Auto white balance',
            'XResolution' => 240,
            'YResolution' => 240,
            'GPSVersionID'  => '2.2.0.0',
            // no data present for the following
            'ImageWidth' => 0,
            'ImageLength' => 0,
            'BitsPerSample' => [0, 0, 0],
            'Compression' => '',
            'PhotometricInterpretation' => '',
            'Orientation' => '',
            'SamplesPerPixel' => 0,
            'PlanarConfiguration' => '',
            'YCbCrSubSampling' => '',
            'YCbCrPositioning' => '',
            // 'StripOffsets' => ?,
            // 'RowsPerStrip' => ?,
            // 'StripByteCounts' => ?,
            // 'JPEGInterchangeFormat' => ?,
            // 'JPEGInterchangeFormatLength' => ?,
            // 'TransferFunction' => ?,
            // 'WhitePoint' => ?,
            // 'PrimaryChromaticities' => ?,
            // 'YCbCrCoefficients' => ?,
            // 'ReferenceBlackWhite' => ?,
            'FlashpixVersion' => '',
            'Gamma' => 0.0,
            'ComponentsConfiguration' => '',
            'CompressedBitsPerPixel' => 0.0,
            'PixelXDimension' => 0,
            'PixelYDimension' => 0,
            'MakerNote' => '',
            'RelatedSoundFile' => '',
            'SpectralSensitivity' => '',
            // 'OECF' => ?,
            'SensitivityType' => '',
            'StandardOutputSensitivity' => 0,
            'RecommendedExposureIndex' => 0,
            'ISOSpeedLatitudeyyy' => 0,
            'ISOSpeedLatitudezzz' => 0,
            'BrightnessValue' => 0.0,
            'SubjectDistance' => 0,
            'LightSource' => '',
            'SubjectArea' => [0,0],
            'FlashEnergy' => 0.0,
            // 'SpatialFrequencyResponse' => ?,
            'SubjectLocation' => [0, 0],
            'ExposureIndex' => 0.0,
            'SensingMethod' => '',
            'FileSource' => '',
            'SceneType' => '',
            // 'CFAPattern' => ?,
            'DigitalZoomRatio' => 0.0,
            'FocalLengthIn35mmFilm' => 0,
            'GainControl' => '',
            'Contrast' => '',
            'Saturation' => '',
            'Sharpness' => '',
            // 'DeviceSettingDescription' => ?,
            'SubjectDistanceRange' => '',
            'Temperature' => 0.0,
            'Humidity' => 0.0,
            'Pressure' => 0.0,
            'WaterDepth' => 0.0,
            'Acceleration' => 0.0,
            'CameraElevationAngle' => 0.0,
            'ImageUniqueID' => '',
            'LensMake' => '',
            'LensSerialNumber' => '',
            'GPSSatellites' => '',
            'GPSStatus' => '',
            'GPSMeasureMode' => '',
            'GPSDOP' => 0.0,
            'GPSSpeedRef' => '',
            'GPSSpeed' => 0.0,
            'GPSTrackRef' => '',
            'GPSTrack' => 0.0,
            'GPSImgDirectionRef' => '',
            'GPSMapDatum'  => '',
            'GPSDestLatitude' => 0.0,
            'GPSDestLongitude' => 0.0,
            'GPSDestBearingRef' => '',
            'GPSDestBearing' => 0.0,
            'GPSDestDistanceRef' => '',
            'GPSDestDistance' => 0.0,
            'GPSProcessingMethod' => '',
            'GPSAreaInformation' => '',
            'GPSDateTimeStamp' => null,
            'GPSDifferential' => '',
            'GPSHPositioningError' => 0.0,
        ];

        $this->assertDtoGettersReturnData($exifDto, $expectedExifData);
    }
}
