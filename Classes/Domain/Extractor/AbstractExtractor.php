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
use Neos\Utility\MediaTypes;

abstract class AbstractExtractor implements ExtractorInterface
{
    /**
     * The media types this adapter can handle
     *
     * @var array
     */
    protected static $compatibleMediaTypes = [];

    /**
     * @inheritDoc
     */
    public static function isSuitableFor(FlowResource $resource)
    {
        $mediaType = $resource->getMediaType();
        foreach (static::$compatibleMediaTypes as $compatibleMediaType) {
            if (MediaTypes::mediaRangeMatches($compatibleMediaType, $mediaType)) {
                return true;
            }
        }

        return false;
    }
}
