<?php

declare(strict_types=1);
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
use Neos\MetaData\Extractor\Converter\DateConverter;
use Neos\MetaData\Extractor\Domain\Dto\ExtractedMetaData;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\Extractor\Specifications\Iptc;

/**
 * @Flow\Scope("singleton")
 * @see https://www.iptc.org/std/IIM/4.2/specification/IIMV4.2.pdf
 */
class IptcIimExtractor extends AbstractExtractor
{
    /**
     * @var string[]
     */
    protected static array $compatibleMediaTypes = [
        'application/octet-stream',
        'application/x-shockwave-flash',
        'image/bmp',
        'image/gif',
        'image/iff',
        'image/jp2',
        'image/jpeg',
        'image/png',
        'image/psd',
        'image/tiff',
        'image/vnd.microsoft.icon',
        'image/vnd.wap.wbmp',
        'image/xbm',
    ];

    /**
     * @var string[]
     */
    protected static array $mapping = [
        'City' => Iptc\Iim::CITY,
        'Contact' => Iptc\Iim::CONTACT,
        'CopyrightNotice' => Iptc\Iim::COPYRIGHT_NOTICE,
        'Country' => Iptc\Iim::COUNTRY_PRIMARY_LOCATION_NAME,
        'CountryCode' => Iptc\Iim::COUNTRY_PRIMARY_LOCATION_CODE,
        'Creator' => Iptc\Iim::BYLINE,
        'CreatorTitle' => Iptc\Iim::BYLINE_TITLE,
        'CreditLine' => Iptc\Iim::CREDIT,
        'Description' => Iptc\Iim::CAPTION_ABSTRACT,
        'DescriptionWriter' => Iptc\Iim::WRITER_EDITOR,
        'Headline' => Iptc\Iim::HEADLINE,
        'Instructions' => Iptc\Iim::SPECIAL_INSTRUCTIONS,
        'IntellectualGenres' => Iptc\Iim::OBJECT_ATTRIBUTE_REFERENCE,
        'JobId' => Iptc\Iim::ORIGINAL_TRANSMISSION_REFERENCE,
        'Keywords' => Iptc\Iim::KEYWORDS,
        'Source' => Iptc\Iim::SOURCE,
        'State' => Iptc\Iim::PROVINCE_STATE,
        'SubjectCodes' => Iptc\Iim::SUBJECT_REFERENCE,
        'Sublocation' => Iptc\Iim::SUBLOCATION,
        'Title' => Iptc\Iim::OBJECT_NAME,
    ];

    /**
     * @var string[][]
     */
    protected static array $dateTimeMapping = [
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
    public function extractMetaData(FlowResource $resource): ExtractedMetaData
    {
        try {
            \getimagesize($resource->createTemporaryLocalCopy(), $fileInfo);
        } catch (FlowResourceException $exception) {
            throw new ExtractorException(
                'Could not extract IPTC data from ' . $resource->getFilename(),
                1484059892,
                $exception
            );
        }
        if (!isset($fileInfo['APP13'])) {
            throw new ExtractorException(
                'Could not find "APP13" section in file info of ' . $resource->getFilename(),
                1484059903
            );
        }
        $iimData = \iptcparse($fileInfo['APP13']);
        if ($iimData === false) {
            throw new ExtractorException('Could not parse IPTC data of ' . $resource->getFilename(), 1484059912);
        }

        $iim = new Iptc\Iim($iimData);

        $iptcData = array_map(static function ($iimProperty) use ($iim) {
            return $iim->getProperty($iimProperty);
        }, static::$mapping);

        foreach (static::$dateTimeMapping as $iptcProperty => $iimProperties) {
            $dateString = $iim->getProperty($iimProperties['date']);
            if (\is_string($dateString) && $dateString !== '') {
                $timeString = $iim->getProperty($iimProperties['time']);
                $iptcData[$iptcProperty] = DateConverter::convertIso8601DateAndTimeString(
                    $dateString,
                    \is_string($timeString) ? $timeString : null
                );
            }
        }

        //caring for deprecated (supplemental) category
        /** @var string[] $categories */
        $categories = [];
        $supplementalCategories = $iim->getProperty(Iptc\Iim::SUPPLEMENTAL_CATEGORY);
        if (\is_array($supplementalCategories)) {
            $categories = $supplementalCategories;
        } else {
            $categories[] = $supplementalCategories;
        }
        $category = $iim->getProperty(Iptc\Iim::CATEGORY);
        if (\is_string($category)) {
            $categories[] = $category;
        }
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
            } elseif (\is_array($iptcData['SubjectCodes'])) {
                $iptcData['SubjectCodes'] = \array_merge($iptcData['SubjectCodes'], $subjectCodesFromCategories);
            } else {
                $iptcData['SubjectCodes'] = \array_merge([$iptcData['SubjectCodes']], $subjectCodesFromCategories);
            }
        }
        if (!empty($deprecatedCategories)) {
            $iptcData['DeprecatedCategories'] = $deprecatedCategories;
        }

        $metaData = new ExtractedMetaData();
        foreach ($iptcData as $property => $value) {
            if ($value === '' || $value === []) {
                continue;
            }
            $metaData->set('iptc.' . $property, self::convertValueForMetadata($value));
        }

        return $metaData;
    }
}
