<?php
namespace Neos\MetaData\Extractor\Tests\Functional\Domain\Extractor\Adapter;

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
use Neos\MetaData\Extractor\Domain\Extractor\Adapter\InterventionImageAdapter;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTest;
use TYPO3\Flow\Reflection\ObjectAccess;


class InterventionImageAdapterTest extends AbstractExtractorTest
{

    /**
     * @var InterventionImageAdapter
     */
    protected $interventionImageAdapter;


    public function setUp()
    {
        parent::setUp();

        $this->interventionImageAdapter = new InterventionImageAdapter();
    }

    /**
     * @test
     */
    public function extractExifData() {
        $metaDataCollection = new MetaDataCollection();
        $this->interventionImageAdapter->extractMetaData($this->testAsset->getResource(), $metaDataCollection);
        $exifDto = $metaDataCollection->get('exif');

        $this->assertInstanceOf(Dto\Exif::class, $exifDto);

        $expectedExifData = [
            'Artist' => 'Daniel Lienert',
            'ColorSpace' =>'sRGB',
            'Copyright' => '© Daniel Lienert',
            'ExposureTime' => '1/640',
            'FNumber' => 8.0,
            'GPSLatitude' => -46.659787000000001,
            'GPSLongitude' => 168.84702999999999,
            'ISOSpeedRatings' => 100,
            'ImageDescription' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'Make' => 'Canon',
            'Model' => 'Canon EOS 5D Mark II',
            'FocalLength' => 24,
            'XResolution' => 240,
            'YResolution' => 240
        ];

        $this->assertDtoGettersReturnData($exifDto, $expectedExifData);
    }


    /**
     * @test
     */
    public function extractIptcData() {
        $metaDataCollection = new MetaDataCollection();
        $this->interventionImageAdapter->extractMetaData($this->testAsset->getResource(), $metaDataCollection);
        $iptcDto = $metaDataCollection->get('iptc');

        $this->assertInstanceOf(Dto\Iptc::class, $iptcDto);

        $expectedIptcData = [
            'AuthorByline' => 'Daniel Lienert',
            'Caption' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'Category' => 'Lig',
            'City' => 'Otara',
            'Country' => 'Newzealand',
            'CreationDate' => \DateTime::createFromFormat('YmdHis', '20130918105911'),
            'Keywords' => ['Beste', 'Leuchtturm', 'Neu Seeland', 'Neuseeland', 'New Zealand'],
            'State' => 'Southland',
            'SubCategories' => 'Nature',
            'Title' => 'Waipapa Point Leuchtturm',
            'SubLocation' => 'Waipapa Point Lighthouse',
        ];

        $this->assertDtoGettersReturnData($iptcDto, $expectedIptcData);
    }

    /**
     * @param Dto\AbstractMetaDataDto $dto
     * @param array $expectedDtoData
     */
    protected function assertDtoGettersReturnData(Dto\AbstractMetaDataDto $dto, array $expectedDtoData) {
        foreach ($expectedDtoData as $key => $value) {
            $getter = 'get' . $key;
            $this->assertEquals($value, $dto->$getter());
        }
    }
}