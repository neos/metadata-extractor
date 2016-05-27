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

use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Extractor\Exception\NoExtractorAvailableException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Object\Exception\UnknownObjectException;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Resource\Resource as FlowResource;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\Extractor\ExtractorInterface;
use TYPO3\Media\Domain\Model\Asset;

class ExtractionManager
{

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @param Asset $asset
     * @return MetaDataCollection
     * @throws NoExtractorAvailableException
     * @throws UnknownObjectException
     */
    public function extractMetaData(Asset $asset) {
        $flowResource = $asset->getResource();
        $suitableAdapterClasses = $this->findSuitableExtractorAdaptersForResource($flowResource);

        if(count($suitableAdapterClasses) == 0) {
            throw new NoExtractorAvailableException('No Extractor available for media type ' . $flowResource->getMediaType(), 1461433352);
        }

        $metaDataCollection = new MetaDataCollection();
        $this->buildAssetMetaData($asset, $metaDataCollection);

        foreach($suitableAdapterClasses as $suitableAdapterClass) {
            /** @var ExtractorInterface $suitableAdapter */
            $suitableAdapter = $this->objectManager->get($suitableAdapterClass);
            $suitableAdapter->extractMetaData($flowResource, $metaDataCollection);
        }

        return $metaDataCollection;
    }

    /**
     * @param FlowResource $flowResource
     * @return array
     */
    protected function findSuitableExtractorAdaptersForResource(FlowResource $flowResource) {
        $extractorAdapters = $this->getExtractorAdapters();
        $mediaType = $flowResource->getMediaType();
        $suitableAdapterClasses = [];

        foreach($extractorAdapters as $extractorAdapterClass => $compatibleMediaTypes) {
            if(in_array($mediaType, $compatibleMediaTypes)) {
                $suitableAdapterClasses[] = $extractorAdapterClass;
            }
        }

        return $suitableAdapterClasses;
    }

    /**
     * @return array
     * @throws UnknownObjectException
     */
    protected function getExtractorAdapters() {
        $extractorAdapterClassNames = $this->reflectionService->getAllImplementationClassNamesForInterface(ExtractorInterface::class);
        $extractorAdapters = [];

        /** @var ExtractorInterface $className */
        foreach($extractorAdapterClassNames as $className) {
            $extractorAdapters[$className] = $className::getCompatibleMediaTypes();
        }

        return $extractorAdapters;
    }

    /**
     * @param Asset $asset
     * @param MetaDataCollection $metaDataCollection
     */
    protected function buildAssetMetaData(Asset $asset, MetaDataCollection $metaDataCollection) {

        $tags = [];
        /** @var \TYPO3\Media\Domain\Model\Tag $tagObject */
        foreach ($asset->getTags() as $tagObject) {
            $tags[] = $tagObject->getLabel();
        }

        $assetDto = new Dto\Asset([
            'Caption' => $asset->getCaption(),
            'Identifier' => $asset->getIdentifier(),
            'Title' => $asset->getTitle(),
            'FileName' => $asset->getResource()->getFilename(),
            'Collections' => $asset->getAssetCollections()->getValues(),
            'Tags' => $tags
        ]);
        $metaDataCollection->set('asset', $assetDto);
    }
}