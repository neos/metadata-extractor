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

use ElementareTeilchen\MetaData\Iptc\Iim;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\Extractor\AbstractExtractor;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\Exception as ResourceException;
use TYPO3\Flow\Resource\Resource as FlowResource;

/**
 * Adapter for IPTC IIM
 *
 * @see https://www.iptc.org/std/IIM/4.2/specification/IIMV4.2.pdf
 */
class IptcIimAdapter extends AbstractExtractor
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
     * @var array
     */
    protected $iimData;

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     *
     * @throws ResourceException
     * @throws ExtractorException
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        $iim = new Iim($this->iimData);

        $iptcData = [];
        $iptcData['IntellectualGenres'] = $iim->getProperty(Iim::OBJECT_ATTRIBUTE_REFERENCE);
        $iptcData['Title'] = $iim->getProperty(Iim::OBJECT_NAME);
        $iptcData['SubjectCodes'] = $iim->getProperty(Iim::SUBJECT_REFERENCE);

        //caring for deprecated (supplemental) category
        /** @var array $categories */
        $categories = $iim->getProperty(Iim::SUPPLEMENTAL_CATEGORY);
        $categories[] = $iim->getProperty(Iim::CATEGORY);
        $subjectCodesFromCategories = [];
        $deprecatedCategories = [];
        foreach ($categories as $category) {
            if ($category !== '') {
                $subjectCode = Iim::convertCategoryToSubjectCode($category);
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

        $iptcData['Keywords'] = $iim->getProperty(Iim::KEYWORDS);
        $iptcData['Instructions'] = $iim->getProperty(Iim::SPECIAL_INSTRUCTIONS);

        $creationDateString = $iim->getProperty(Iim::DATE_CREATED);
        if (!empty($creationDateString)) {
            $creationTimeString = $iim->getProperty(Iim::TIME_CREATED);
            $creationDateString .= empty($creationTimeString) ? '000000+0000' : $creationTimeString;
            $iptcData['CreationDate'] = \DateTime::createFromFormat('YmdHisO', $creationDateString);
        }
        $iptcData['Creator'] = $iim->getProperty(Iim::BYLINE);
        $iptcData['CreatorTitle'] = $iim->getProperty(Iim::BYLINE_TITLE);
        $iptcData['City'] = $iim->getProperty(Iim::CITY);
        $iptcData['Sublocation'] = $iim->getProperty(Iim::SUBLOCATION);
        $iptcData['State'] = $iim->getProperty(Iim::PROVINCE_STATE);
        $iptcData['CountryCode'] = $iim->getProperty(Iim::COUNTRY_PRIMARY_LOCATION_CODE);
        $iptcData['Country'] = $iim->getProperty(Iim::COUNTRY_PRIMARY_LOCATION_NAME);
        $iptcData['JobId'] = $iim->getProperty(Iim::ORIGINAL_TRANSMISSION_REFERENCE);
        $iptcData['Headline'] = $iim->getProperty(Iim::HEADLINE);
        $iptcData['CreditLine'] = $iim->getProperty(Iim::CREDIT);
        $iptcData['Source'] = $iim->getProperty(Iim::SOURCE);
        $iptcData['CopyrightNotice'] = $iim->getProperty(Iim::COPYRIGHT_NOTICE);
        $iptcData['Contact'] = $iim->getProperty(Iim::CONTACT);
        $iptcData['Description'] = $iim->getProperty(Iim::CAPTION_ABSTRACT);
        $iptcData['DescriptionWriter'] = $iim->getProperty(Iim::WRITER_EDITOR);

        // sometimes used but not really specified in IPTC MetaData
        $digitalCreationDateString = $iim->getProperty(Iim::DIGITAL_CREATION_DATE);
        if (!empty($digitalCreationDateString)) {
            $digitalCreationTimeString = $iim->getProperty(Iim::DIGITAL_CREATION_TIME);
            $digitalCreationDateString .= empty($digitalCreationTimeString) ? '000000+0000' : $digitalCreationTimeString;
            $iptcData['DigitalCreationDate'] = \DateTime::createFromFormat('YmdHisO', $digitalCreationDateString);
        }
        $metaDataCollection->set('iptc', new Dto\Iptc($iptcData));
    }

    /**
     * @param FlowResource $resource
     *
     * @return bool
     */
    public function canHandleExtraction(FlowResource $resource)
    {
        try {
            getimagesize($resource->createTemporaryLocalCopy(), $fileInfo);

            if (isset($fileInfo['APP13'])) {
                $this->iimData = iptcparse($fileInfo['APP13']);
                if ($this->iimData !== false) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}