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
use Neos\Utility\MediaTypes;

abstract class AbstractExtractor implements ExtractorInterface
{
    /**
     * The media types this adapter can handle
     *
     * @var string[]
     */
    protected static $compatibleMediaTypes = [];

    /**
     * @inheritDoc
     */
    public static function isSuitableFor(PersistentResource $resource) : bool
    {
        $mediaType = $resource->getMediaType();
        foreach (static::$compatibleMediaTypes as $compatibleMediaType) {
            if (MediaTypes::mediaRangeMatches($compatibleMediaType, $mediaType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Converts an extracted value into its scalar representation for further use.
     *
     * Dates are normalized to an ISO date string, arrays to their JSON representation. Floats are kept
     * as strings so that their representation (e.g. "240" instead of "240.0") stays stable.
     *
     * @param mixed $value
     * @return string|int|bool
     */
    protected static function convertValueForMetadata($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (\is_array($value)) {
            return \json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }
        if (\is_float($value)) {
            return (string)$value;
        }
        return $value;
    }
}
