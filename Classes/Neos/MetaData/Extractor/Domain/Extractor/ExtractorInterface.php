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

use Neos\MetaData\Domain\Collection\MetaDataCollection;
use \TYPO3\Flow\Resource\Resource as FlowResource;

interface ExtractorInterface
{

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection);


    /**
     * @param FlowResource $resource
     * @return bool
     */
    public function canHandleExtraction(FlowResource $resource);


    /**
     * @return array
     */
    public static function getCompatibleMediaTypes();
}