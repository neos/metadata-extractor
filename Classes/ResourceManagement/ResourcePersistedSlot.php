<?php
namespace Neos\MetaData\Extractor\ResourceManagement;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use TYPO3\Flow\Annotations as Flow;

class ResourcePersistedSlot
{

    /**
     * @Flow\Inject
     * @var \Neos\MetaData\Extractor\Domain\ExtractionManager
     */
    protected $extractionManager;


    public function extractOnPersistedResource($object, $objectState) {

    }
}