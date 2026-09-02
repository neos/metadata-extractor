<?php

declare(strict_types=1);
namespace Neos\MetaData\Extractor\Domain;

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
use Neos\Flow\ObjectManagement\ObjectManager;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Flow\ResourceManagement\PersistentResource as FlowResource;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\ImageVariant;
use Neos\MetaData\Domain\Dto\MetaDataAssetReference;
use Neos\MetaData\Domain\Dto\MetaDataPropertyName;
use Neos\MetaData\Extractor\Domain\Extractor\ExtractorInterface;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\MetaDataManager;

/**
 * @Flow\Scope("singleton")
 */
class ExtractionManager
{
    /**
     * @Flow\Inject
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var MetaDataManager
     */
    protected $metaDataManager;

    /**
     * @param FlowResource $flowResource
     * @return string[] Class names
     */
    protected function findSuitableExtractorAdaptersForResource(FlowResource $flowResource) : array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $extractorAdapters = $this->reflectionService->getAllImplementationClassNamesForInterface(
            ExtractorInterface::class
        );

        $suitableAdapterClasses = \array_filter(
            $extractorAdapters,
            function ($extractorAdapterClass) use ($flowResource) {
                /** @var ExtractorInterface $extractorAdapterClass */
                return $extractorAdapterClass::isSuitableFor($flowResource);
            }
        );

        return $suitableAdapterClasses;
    }

    /**
     * Extracts all metadata of the given asset and returns it keyed by property name (e.g. 'exif.Model').
     *
     * The extracted values are additionally persisted for every property that is defined in the meta data
     * configuration, see {@see self::persistExtractedValues()}.
     *
     * @param Asset $asset
     * @return array<string, string|int|bool>
     * @throws ExtractorException
     */
    public function extractMetaData(Asset $asset) : array
    {
        if ($asset instanceof ImageVariant) {
            $asset = $asset->getOriginalAsset();
        }

        $flowResource = $asset->getResource();
        if ($flowResource === null) {
            throw new ExtractorException('Resource of Asset "' . $asset->getTitle() . '"" not found.', 1484060541);
        }

        $metaData = [];

        $suitableAdapterClasses = $this->findSuitableExtractorAdaptersForResource($flowResource);
        foreach ($suitableAdapterClasses as $suitableAdapterClass) {
            /** @var ExtractorInterface $suitableAdapter */
            /** @noinspection PhpUnhandledExceptionInspection */
            $suitableAdapter = $this->objectManager->get($suitableAdapterClass);
            try {
                $suitableAdapter->extractMetaData($flowResource, $metaData);
            } catch (ExtractorException $exception) {
                //Extractor is theoretically suitable but failed to extract meta data
                continue;
            }
        }

        $this->persistExtractedValues($asset, $metaData);

        return $metaData;
    }

    /**
     * Persists the extracted values for all properties that are defined in the meta data configuration.
     *
     * Values for properties that are not defined (or that cannot be represented by their configured type)
     * are ignored, so that extraction stays flexible without writing arbitrary data.
     *
     * @param Asset $asset
     * @param array<string, string|int|bool> $metaData
     * @return void
     */
    protected function persistExtractedValues(Asset $asset, array $metaData)
    {
        $assetReference = MetaDataAssetReference::create($asset->getAssetSourceIdentifier(), $asset->getIdentifier());
        $propertyDefinitions = $this->metaDataManager->getPropertyDefinitions();

        foreach ($metaData as $propertyName => $propertyValue) {
            $metaDataPropertyName = MetaDataPropertyName::fromString($propertyName);
            if (!$propertyDefinitions->include($metaDataPropertyName)) {
                continue;
            }
            if (!\is_string($propertyValue) && !\is_int($propertyValue) && !\is_bool($propertyValue)) {
                continue;
            }
            $this->metaDataManager->setMetaDataPropertyValue($assetReference, $metaDataPropertyName, $propertyValue);
        }
    }
}