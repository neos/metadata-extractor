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

use Neos\Flow\ResourceManagement\PersistentResource as FlowResource;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Extractor\Exception\ExtractorException;

interface ExtractorInterface
{
    /**
     * @param FlowResource $resource
     *
     * @return bool
     */
    public static function isSuitableFor(FlowResource $resource);

    /**
     * @param FlowResource $resource
     * @param MetaDataCollection $metaDataCollection
     *
     * @throws ExtractorException
     */
    public function extractMetaData(FlowResource $resource, MetaDataCollection $metaDataCollection);
}
