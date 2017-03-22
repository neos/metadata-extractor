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
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\Extractor\Specifications\Iptc;

/**
 * @Flow\Scope("singleton")
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
        'image/vnd.microsoft.icon',
    ];

    /**
     * @var array
     */
    protected static $mapping = [
        'IntellectualGenres' => Iptc\Iim::OBJECT_ATTRIBUTE_REFERENCE,
        'Title' => Iptc\Iim::OBJECT_NAME,
        'SubjectCodes' => Iptc\Iim::SUBJECT_REFERENCE,
        'Keywords' => Iptc\Iim::KEYWORDS,
        'Instructions' => Iptc\Iim::SPECIAL_INSTRUCTIONS,
        'Creator' => Iptc\Iim::BYLINE,
        'CreatorTitle' => Iptc\Iim::BYLINE_TITLE,
        'City' => Iptc\Iim::CITY,
        'Sublocation' => Iptc\Iim::SUBLOCATION,
        'State' => Iptc\Iim::PROVINCE_STATE,
        'CountryCode' => Iptc\Iim::COUNTRY_PRIMARY_LOCATION_CODE,
        'Country' => Iptc\Iim::COUNTRY_PRIMARY_LOCATION_NAME,
        'JobId' => Iptc\Iim::ORIGINAL_TRANSMISSION_REFERENCE,
        'Headline' => Iptc\Iim::HEADLINE,
        'CreditLine' => Iptc\Iim::CREDIT,
        'Source' => Iptc\Iim::SOURCE,
        'CopyrightNotice' => Iptc\Iim::COPYRIGHT_NOTICE,
        'Contact' => Iptc\Iim::CONTACT,
        'Description' => Iptc\Iim::CAPTION_ABSTRACT,
        'DescriptionWriter' => Iptc\Iim::WRITER_EDITOR,
    ];

    /**
     * @var array
     */
    protected static $dateTimeMapping = [
        'CreationDate' => [
            'date' => Iptc\Iim::DATE_CREATED,
            'time' => Iptc\Iim::TIME_CREATED,
        ],
        // sometimes used but not really specified in IPTC MetaData
        'DigitalCreationDate' => [
            'date' => Iptc\Iim::DIGITAL_CREATION_DATE,
            'time' => Iptc\Iim::DIGITAL_CREATION_TIME,
        ],
    ];

    /**
     * @inheritdoc
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection)
    {
        try {
            getimagesize($resource->createTemporaryLocalCopy(), $fileInfo);
        } catch (FlowResourceException $exception) {
            throw new ExtractorException('Could not extract IPTC data from ' . $resource->getFilename(), 1484059892, $exception);
        }
        if (!isset($fileInfo['APP13'])) {
            throw new ExtractorException('Could not find "APP13" section in file info of ' . $resource->getFilename(), 1484059903);
        }
        $iimData = iptcparse($fileInfo['APP13']);
        if ($iimData === false) {
            throw new ExtractorException('Could not parse IPTC data of ' . $resource->getFilename(), 1484059912);
        }

        $iim = new Iptc\Iim($iimData);

        $iptcData = [];

        foreach (static::$mapping as $iptcProperty => $iimProperty) {
            $iptcData[$iptcProperty] = $iim->getProperty($iimProperty);
        }

        foreach (static::$dateTimeMapping as $iptcProperty => $iimProperties) {
            $dateString = $iim->getProperty($iimProperties['date']);
            if (!empty($dateString)) {
                $timeString = $iim->getProperty($iimProperties['time']);
                $dateTimeString = $dateString;
                if (empty($timeString)) {
                    $dateTimeString .= '000000+0000';
                } else {
                    $dateTimeString .= $timeString;
                    if ((strpos($timeString, '+') === false && strpos($timeString, '-')) === false) {
                        $dateTimeString .= '+0000';
                    }
                }
                $iptcData[$iptcProperty] = \DateTime::createFromFormat('YmdHisO', $dateTimeString);
            }
        }

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

        $metaDataCollection->set('iptc', new Dto\Iptc($iptcData));
    }
}
