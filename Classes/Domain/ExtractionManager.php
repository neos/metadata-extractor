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
use Neos\Flow\Reflection\Exception\ClassLoadingForReflectionFailedException;
use Neos\Flow\Reflection\Exception\InvalidClassException;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Flow\ResourceManagement\PersistentResource as FlowResource;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\ImageVariant;
use Neos\MetaData\Domain\Dto\MetaDataAssetReference;
use Neos\MetaData\Domain\Dto\MetaDataPropertyName;
use Neos\MetaData\Extractor\Domain\Dto\ExtractedMetaData;
use Neos\MetaData\Extractor\Domain\Extractor\ExtractorInterface;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\MetaDataManager;

#[Flow\Scope('singleton')]
class ExtractionManager
{
    #[Flow\Inject]
    protected ObjectManager $objectManager;

    #[Flow\Inject]
    protected ReflectionService $reflectionService;

    #[Flow\Inject]
    protected MetaDataManager $metaDataManager;

    /**
     * @return string[] Class names
     * @throws \ReflectionException
     * @throws ClassLoadingForReflectionFailedException
     * @throws InvalidClassException
     */
    protected function findSuitableExtractorAdaptersForResource(FlowResource $flowResource) : array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $extractorAdapters = $this->reflectionService->getAllImplementationClassNamesForInterface(
            ExtractorInterface::class
        );

        return \array_filter(
            $extractorAdapters,
            static function ($extractorAdapterClass) use ($flowResource) {
                /** @var ExtractorInterface $extractorAdapterClass */
                return $extractorAdapterClass::isSuitableFor($flowResource);
            }
        );
    }

    /**
     * Extracts all metadata of the given asset and returns it as a collection of named, scalar values
     * (e.g. {@code exif.Model}, {@code iptc.Title}).
     *
     * The extracted values are additionally persisted for every property that is defined in the meta data
     * configuration, see {@see self::persistExtractedValues()}.
     *
     * Extractors that throw an {@see ExtractorException} are skipped, all others are processed.
     */
    public function extractMetaData(Asset $asset) : ExtractedMetaData
    {
        if ($asset instanceof ImageVariant) {
            $asset = $asset->getOriginalAsset();
        }

        $flowResource = $asset->getResource();

        $metaData = new ExtractedMetaData();

        $suitableAdapterClasses = $this->findSuitableExtractorAdaptersForResource($flowResource);
        foreach ($suitableAdapterClasses as $suitableAdapterClass) {
            /** @var ExtractorInterface $suitableAdapter */
            /** @noinspection PhpUnhandledExceptionInspection */
            $suitableAdapter = $this->objectManager->get($suitableAdapterClass);
            try {
                $adapterData = $suitableAdapter->extractMetaData($flowResource);
                foreach ($adapterData as $propertyName => $propertyValue) {
                    $metaData->set($propertyName, $propertyValue);
                }
            } catch (ExtractorException) {
                // Extractor is theoretically suitable but failed to extract metadata
                continue;
            }
        }

        $this->persistExtractedValues($asset, $metaData);

        return $metaData;
    }

    /**
     * Persists the extracted values for all properties that are defined in the metadata configuration.
     *
     * Values for properties that are not defined are ignored, so that extraction stays flexible without
     * writing arbitrary data.
     */
    protected function persistExtractedValues(Asset $asset, ExtractedMetaData $metaData): void
    {
        $assetReference = MetaDataAssetReference::create($asset->getAssetSourceIdentifier(), $asset->getIdentifier());
        $propertyDefinitions = $this->metaDataManager->getPropertyDefinitions();

        foreach ($metaData as $propertyName => $propertyValue) {
            $metaDataPropertyName = MetaDataPropertyName::fromString($propertyName);
            if (!$propertyDefinitions->include($metaDataPropertyName)) {
                continue;
            }
            $this->metaDataManager->setMetaDataPropertyValue($assetReference, $metaDataPropertyName, $propertyValue);
        }
    }
}
