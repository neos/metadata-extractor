<?php
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
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\ImageVariant;
use Neos\Media\Domain\Model\Tag;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\Extractor\ExtractorInterface;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\MetaData\MetaDataManager;

/**
 * ExtractionManager
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
     * @param Asset $asset
     *
     * @return MetaDataCollection
     * @throws ExtractorException
     */
    public function extractMetaData(Asset $asset)
    {
        if ($asset instanceof ImageVariant) {
            $asset = $asset->getOriginalAsset();
        }

        $flowResource = $asset->getResource();
        if ($flowResource === null) {
            throw new ExtractorException('Resource of Asset "' . $asset->getTitle() . '"" not found.', 1484060541);
        }

        $metaDataCollection = new MetaDataCollection();
        $this->buildAssetMetaData($asset, $metaDataCollection);

        $suitableAdapterClasses = $this->findSuitableExtractorAdaptersForResource($flowResource);
        foreach ($suitableAdapterClasses as $suitableAdapterClass) {
            /** @var ExtractorInterface $suitableAdapter */
            $suitableAdapter = $this->objectManager->get($suitableAdapterClass);
            try {
                $suitableAdapter->extractMetaData($flowResource, $metaDataCollection);
            } catch (ExtractorException $exception) {
                //Extractor is theoretically suitable but failed to extract meta data
                continue;
            }
        }

        $this->metaDataManager->updateMetaDataForAsset($asset, $metaDataCollection);

        return $metaDataCollection;
    }

    /**
     * @param Asset $asset
     * @param MetaDataCollection $metaDataCollection
     */
    protected function buildAssetMetaData(Asset $asset, MetaDataCollection $metaDataCollection)
    {
        $tags = [];
        foreach ($asset->getTags() as $tagObject) {
            /** @var Tag $tagObject */
            $tags[] = $tagObject->getLabel();
        }

        $collections = [];
        foreach ($asset->getAssetCollections() as $collectionObject) {
            /** @var AssetCollection $collectionObject */
            $collections[] = $collectionObject->getTitle();
        }

        $assetDto = new Dto\Asset([
            'Caption' => $asset->getCaption(),
            'Identifier' => $asset->getIdentifier(),
            'Title' => $asset->getTitle(),
            'FileName' => $asset->getResource()->getFilename(),
            'Collections' => $collections,
            'Tags' => $tags,
            'AssetObject' => $asset,
        ]);
        $metaDataCollection->set('asset', $assetDto);
    }

    /**
     * @param FlowResource $flowResource
     *
     * @return array
     */
    protected function findSuitableExtractorAdaptersForResource(FlowResource $flowResource)
    {
        $extractorAdapters = $this->reflectionService->getAllImplementationClassNamesForInterface(ExtractorInterface::class);

        $suitableAdapterClasses = array_filter($extractorAdapters, function ($extractorAdapterClass) use ($flowResource) {
            /** @var ExtractorInterface $extractorAdapterClass */
            return $extractorAdapterClass::isSuitableFor($flowResource);
        });

        return $suitableAdapterClasses;
    }
}
