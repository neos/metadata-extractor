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

        $iptc = $image->iptc();
        if(is_array($iptc)) {
            $metaDataCollection->set('iptc', new Dto\Iptc($iptc));
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
        $exifData['FNumber'] = NumberConverter::convertRationalToFloat($exifData['FNumber']);
        $exifData['FocalLength'] = (int) NumberConverter::convertRationalToFloat($exifData['FocalLength']);
        $exifData['XResolution'] = (int) NumberConverter::convertRationalToFloat($exifData['XResolution']);
        $exifData['YResolution'] = (int) NumberConverter::convertRationalToFloat($exifData['YResolution']);

        $exifData['GPSLongitude'] = GpsConverter::convertRationalArrayAndReferenceToFloat($exifData['GPSLongitude'], $exifData['GPSLongitudeRef']);
        $exifData['GPSLatitude'] = GpsConverter::convertRationalArrayAndReferenceToFloat($exifData['GPSLatitude'], $exifData['GPSLatitudeRef']);

        $exifData['ColorSpace'] = $this->colorSpaceUtility->translateColorSpaceId($exifData['ColorSpace']);

        return new Dto\Exif($exifData);
    }
}