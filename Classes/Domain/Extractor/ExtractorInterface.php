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
use Neos\MetaData\Extractor\Domain\Dto\ExtractedMetaData;
use Neos\MetaData\Extractor\Exception\ExtractorException;

interface ExtractorInterface
{
    public static function isSuitableFor(PersistentResource $resource) : bool;

    /**
     * Extracts the metadata of the given resource as a collection of named, scalar property values.
     *
     * Property names are prefixed with the extractor type (e.g. {@code exif.Model}, {@code iptc.Title}).
     * Values must be scalar (string, integer or boolean): dates are normalized to an ISO date string,
     * arrays to their JSON representation.
     *
     * @throws ExtractorException
     */
    public function extractMetaData(PersistentResource $resource): ExtractedMetaData;
}
