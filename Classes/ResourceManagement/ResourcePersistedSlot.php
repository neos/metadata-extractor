<?php
namespace Neos\MetaData\Extractor\ResourceManagement;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\MetaData\Extractor\Domain\ExtractionManager;
use TYPO3\Flow\Annotations as Flow;

/**
 * Resource Persisted Slot
 */
class ResourcePersistedSlot
{
    /**
     * @Flow\Inject
     * @var ExtractionManager
     */
    protected $extractionManager;

    /**
     * @param $object
     * @param $objectState
     */
    public function extractOnPersistedResource($object, $objectState)
    {

    }
}
