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

use Neos\MetaData\Extractor\Domain\Extractor\ExifExtractor;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTestCase;

class ExifExtractorTest extends AbstractExtractorTestCase
{
    /**
     * @var ExifExtractor
     */
    protected $exifExtractor;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->exifExtractor = new ExifExtractor();
    }

    /**
     * @test
     */
    public function extractMetaData()
    {
        $extractedData = [];

        $this->exifExtractor->extractMetaData($this->testAsset->getResource(), $extractedData);

        $expectedExifData = [
            'exif.ApertureValue' => 6.0,
            'exif.Artist' => 'Daniel Lienert',
            'exif.BodySerialNumber' => '1330801819',
            'exif.CameraOwnerName' => 'Daniel Lienert',
            'exif.ColorSpace' => 'sRGB',
            'exif.Copyright' => '© Daniel Lienert',
            'exif.CustomRendered' => 'Normal process',
            'exif.DateTime' => '2016-04-07 07:09:47',
            'exif.DateTimeDigitized' => '2013-09-17 23:59:11',
            'exif.DateTimeOriginal' => '2013-09-18 10:59:11',
            'exif.ExifVersion' => '0230',
            'exif.ExposureBiasValue' => 0.0,
            'exif.ExposureMode' => 'Manual exposure',
            'exif.ExposureProgram' => 'Manual',
            'exif.ExposureTime' => 0.0015625,
            'exif.Flash' => 'Flash did not fire. No strobe return detection function. Compulsory flash suppression.'
                . ' Flash function present. No red-eye reduction mode or unknown.',
            'exif.FNumber' => 8.0,
            'exif.FocalLength' => 24,
            'exif.FocalPlaneResolutionUnit' => 'inches',
            'exif.FocalPlaneXResolution' => 3849.2117888965,
            'exif.FocalPlaneYResolution' => 3908.1419624217,
            'exif.GPSAltitude' => 17.8953,
            'exif.GPSImgDirection' => 180.0,
            'exif.GPSLatitude' => -46.659787,
            'exif.GPSLongitude' => 168.84703,
            'exif.GPSVersionID' => '2.2.0.0',
            'exif.ImageDescription' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'exif.LensModel' => 'EF24-105mm f/4L IS USM',
            'exif.LensSpecification' => '[24,105,0,0]',
            'exif.Make' => 'Canon',
            'exif.MaxApertureValue' => 4.0,
            'exif.MeteringMode' => 'Pattern',
            'exif.Model' => 'Canon EOS 5D Mark II',
            'exif.PhotographicSensitivity' => 100,
            'exif.ResolutionUnit' => 'inches',
            'exif.SceneCaptureType' => 'Standard',
            'exif.ShutterSpeedValue' => 9.321928,
            'exif.Software' => 'Adobe Photoshop Lightroom 6.3 (Macintosh)',
            'exif.UserComment' => 'Great weather',
            'exif.WhiteBalance' => 'Auto white balance',
            'exif.XResolution' => 240,
            'exif.YResolution' => 240,
        ];

        $this->assertExtractedData($expectedExifData, $extractedData);
    }
}
