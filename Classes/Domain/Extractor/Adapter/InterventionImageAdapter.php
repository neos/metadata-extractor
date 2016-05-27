<?php
namespace Neos\MetaData\Extractor\Domain\Extractor\Adapter;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Intervention\Image\ImageManager;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Converter\GpsConverter;
use Neos\MetaData\Extractor\Converter\NumberConverter;
use Neos\MetaData\Extractor\Domain\Extractor\AbstractExtractor;
use TYPO3\Flow\Resource\Resource as FlowResource;
use TYPO3\Flow\Annotations as Flow;

class InterventionImageAdapter extends AbstractExtractor
{

    /**
     * @Flow\Inject
     * @var \Neos\MetaData\Extractor\Utility\ColorSpace
     */
    protected $colorSpaceUtility;

    /**
     * @var array
     */
    protected static $compatibleMediaTypes = [
        'image/jpeg',
        'video/jpeg',
    ];

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     * @throws \TYPO3\Flow\Resource\Exception
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($resource->createTemporaryLocalCopy());

        $iptcData = $image->iptc();

        if(is_array($iptcData)) {
            $metaDataCollection->set('iptc', $this->buildIptcDto($iptcData));
        }

        $exifData = $image->exif();
        if(is_array($exifData)) {
            $metaDataCollection->set('exif', $this->buildExifDto($exifData));
        }
    }

    /**
     * @param FlowResource $resource
     * @return bool
     */
    public function canHandleExtraction(FlowResource $resource)
    {
        return class_exists(ImageManager::class);
    }

    /**
     * @param $exifData
     * @return Dto\Exif
     */
    protected function buildExifDto($exifData)
    {

        $exifData['Aperture'] = isset($exifData['FNumber']) ? NumberConverter::convertRationalToFloat($exifData['FNumber']) : 0.0;
        $exifData['FocalLength'] = isset($exifData['FocalLength']) ? (int) NumberConverter::convertRationalToFloat($exifData['FocalLength']) : 0;
        $exifData['XResolution'] =  isset($exifData['XResolution']) ? (int) NumberConverter::convertRationalToFloat($exifData['XResolution']) : 0;
        $exifData['YResolution'] =  isset($exifData['YResolution']) ? (int) NumberConverter::convertRationalToFloat($exifData['YResolution']) : 0;
        $exifData['ColorSpace'] = isset($exifData['ColorSpace']) ? $this->colorSpaceUtility->translateColorSpaceId($exifData['ColorSpace']) : '';
        $exifData['Description'] = isset($exifData['ImageDescription']) ? $exifData['ImageDescription'] : '';

        if(isset($exifData['GPSLongitude']) && isset($exifData['GPSLongitudeRef']) && isset($exifData['GPSLatitude']) && isset($exifData['GPSLatitudeRef'])) {
            $exifData['GPSLongitude'] = GpsConverter::convertRationalArrayAndReferenceToFloat($exifData['GPSLongitude'], $exifData['GPSLongitudeRef']);
            $exifData['GPSLatitude'] = GpsConverter::convertRationalArrayAndReferenceToFloat($exifData['GPSLatitude'], $exifData['GPSLatitudeRef']);
        }

        return new Dto\Exif($exifData);
    }

    /**
     * @param $iptcData
     * @return Dto\Iptc
     */
    protected function buildIptcDto($iptcData) {
        $iptcData['Title'] = isset($iptcData['DocumentTitle']) ? $iptcData['DocumentTitle'] : '';
        $iptcData['Description'] =  isset($iptcData['Caption']) ? $iptcData['Caption'] : '';
        $iptcData['SubCategories'] =  isset($iptcData['Subcategories']) ? $iptcData['Subcategories'] : '';

        if(isset($iptcData['CreationDate'])) {
            $creationDateString =  $iptcData['CreationDate'];
            $creationDateString .= isset($iptcData['CreationTime']) ? $iptcData['CreationTime'] : '000000';
            $iptcData['CreationDate'] = \DateTime::createFromFormat('YmdHis', $creationDateString);
        }

        return new Dto\Iptc($iptcData);
    }
}