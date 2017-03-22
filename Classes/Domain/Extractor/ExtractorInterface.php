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

use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Extractor\Exception\ExtractorException;

interface ExtractorInterface
{
    /**
     * @param PersistentResource $resource
     *
     * @return bool
     */
    public static function isSuitableFor(PersistentResource $resource);

    /**
     * @param PersistentResource $resource
     * @param MetaDataCollection $metaDataCollection
     *
     * @throws ExtractorException
     */
    public function extractMetaData(PersistentResource $resource, MetaDataCollection $metaDataCollection);
}
