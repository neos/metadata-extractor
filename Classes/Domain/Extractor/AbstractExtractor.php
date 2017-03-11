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

use TYPO3\Flow\Utility\MediaTypes;
use TYPO3\Flow\ResourceManagement\PersistentResource as PersistentResource;

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
    public static function isSuitableFor(PersistentResource $resource)
    {
        foreach (static::$compatibleMediaTypes as $compatibleMediaType) {
            if(MediaTypes::mediaRangeMatches($compatibleMediaType, $resource->getMediaType()) === true) {
                return true;
            };
        }

        return false;
    }
}
