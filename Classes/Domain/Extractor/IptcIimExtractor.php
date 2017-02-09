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

use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\Extractor\Specifications\Iptc;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\Resource as FlowResource;

/**
 * @Flow\Scope("singleton")
 *
 * @see https://www.iptc.org/std/IIM/4.2/specification/IIMV4.2.pdf
 */
class IptcIimExtractor extends AbstractExtractor
{
    /**
     * @var array
     */
    protected static $compatibleMediaTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'application/x-shockwave-flash',
        'image/psd',
        'image/bmp',
        'image/tiff',
        'application/octet-stream',
        'image/jp2',
        'image/iff',
        'image/vnd.wap.wbmp',
        'image/xbm',
        'image/vnd.microsoft.icon'
    ];

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        $iim = new Iptc\Iim($this->readRawIptcData($resource));

        $iptcData = [];
        $iptcData['IntellectualGenres'] = $iim->getProperty(Iptc\Iim::OBJECT_ATTRIBUTE_REFERENCE);
        $iptcData['Title'] = $iim->getProperty(Iptc\Iim::OBJECT_NAME);
        $iptcData['SubjectCodes'] = $iim->getProperty(Iptc\Iim::SUBJECT_REFERENCE);

        //caring for deprecated (supplemental) category
        /** @var array $categories */
        $categories = $iim->getProperty(Iptc\Iim::SUPPLEMENTAL_CATEGORY);
        $categories[] = $iim->getProperty(Iptc\Iim::CATEGORY);
        $subjectCodesFromCategories = [];
        $deprecatedCategories = [];
        foreach ($categories as $category) {
            if ($category !== '') {
                $subjectCode = Iptc\Iim::convertCategoryToSubjectCode($category);
                if ($subjectCode !== false) {
                    $subjectCodesFromCategories[] = $subjectCode;
                } else {
                    $deprecatedCategories[] = $category;
                }
            }
        }
        if (!empty($subjectCodesFromCategories)) {
            if (!isset($iptcData['SubjectCodes'])) {
                $iptcData['SubjectCodes'] = $subjectCodesFromCategories;
            } else {
                $iptcData['SubjectCodes'] = array_merge($iptcData['SubjectCodes'], $subjectCodesFromCategories);
            }
        }
        if (!empty($deprecatedCategories)) {
            $iptcData['DeprecatedCategories'] = $deprecatedCategories;
        }

        $iptcData['Keywords'] = $iim->getProperty(Iptc\Iim::KEYWORDS);
        $iptcData['Instructions'] = $iim->getProperty(Iptc\Iim::SPECIAL_INSTRUCTIONS);

        $creationDateString = $iim->getProperty(Iptc\Iim::DATE_CREATED);
        if (empty($creationDateString)) {
            $creationTimeString = $iim->getProperty(Iptc\Iim::TIME_CREATED);
            $creationDateString .= empty($creationTimeString) ? '000000+0000' : $creationTimeString;
            $iptcData['CreationDate'] = \DateTime::createFromFormat('YmdHisO', $creationDateString);
        } else {
            $iptcData['CreationDate'] = \DateTime::createFromFormat('Ymd', $creationDateString);
        }
        
        $iptcData['Creator'] = $iim->getProperty(Iptc\Iim::BYLINE);
        $iptcData['CreatorTitle'] = $iim->getProperty(Iptc\Iim::BYLINE_TITLE);
        $iptcData['City'] = $iim->getProperty(Iptc\Iim::CITY);
        $iptcData['Sublocation'] = $iim->getProperty(Iptc\Iim::SUBLOCATION);
        $iptcData['State'] = $iim->getProperty(Iptc\Iim::PROVINCE_STATE);
        $iptcData['CountryCode'] = $iim->getProperty(Iptc\Iim::COUNTRY_PRIMARY_LOCATION_CODE);
        $iptcData['Country'] = $iim->getProperty(Iptc\Iim::COUNTRY_PRIMARY_LOCATION_NAME);
        $iptcData['JobId'] = $iim->getProperty(Iptc\Iim::ORIGINAL_TRANSMISSION_REFERENCE);
        $iptcData['Headline'] = $iim->getProperty(Iptc\Iim::HEADLINE);
        $iptcData['CreditLine'] = $iim->getProperty(Iptc\Iim::CREDIT);
        $iptcData['Source'] = $iim->getProperty(Iptc\Iim::SOURCE);
        $iptcData['CopyrightNotice'] = $iim->getProperty(Iptc\Iim::COPYRIGHT_NOTICE);
        $iptcData['Contact'] = $iim->getProperty(Iptc\Iim::CONTACT);
        $iptcData['Description'] = $iim->getProperty(Iptc\Iim::CAPTION_ABSTRACT);
        $iptcData['DescriptionWriter'] = $iim->getProperty(Iptc\Iim::WRITER_EDITOR);

        // sometimes used but not really specified in IPTC MetaData
        $digitalCreationDateString = $iim->getProperty(Iptc\Iim::DIGITAL_CREATION_DATE);
        if (!empty($digitalCreationDateString)) {
            $digitalCreationTimeString = $iim->getProperty(Iptc\Iim::DIGITAL_CREATION_TIME);
            $digitalCreationDateString .= empty($digitalCreationTimeString) ? '000000+0000' : $digitalCreationTimeString;
            $iptcData['DigitalCreationDate'] = \DateTime::createFromFormat('YmdHisO', $digitalCreationDateString);
        }
        $metaDataCollection->set('iptc', new Dto\Iptc($iptcData));
    }

    /**
     * @param FlowResource $resource
     * @return array
     * @throws ExtractorException
     */
    public function readRawIptcData(FlowResource $resource)
    {
        $iimData = [];
        getimagesize($resource->createTemporaryLocalCopy(), $fileInfo);

        if (isset($fileInfo['APP13'])) {
            $iimData = iptcparse($fileInfo['APP13']);
            if ($iimData === false) {
                throw new ExtractorException(sprintf('IPTC/IIM data of flow resource %s could not be extracted.', $resource->getSha1()), 1486676374);
            }
        }

        return $iimData;
    }
}