<?php
namespace Neos\MetaData\Extractor\ResourceManagement;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\MetaData\Extractor\Domain\ExtractionManager;

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
