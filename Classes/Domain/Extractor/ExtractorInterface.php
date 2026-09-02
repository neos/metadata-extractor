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
use Neos\MetaData\Extractor\Exception\ExtractorException;

interface ExtractorInterface
{
    /**
     * @param PersistentResource $resource
     * @return bool
     */
    public static function isSuitableFor(PersistentResource $resource) : bool;

    /**
     * Extracts the metadata of the given resource and writes the values into $metaData under their
     * property names (e.g. 'exif.Model' or 'iptc.City').
     *
     * Values must be scalar (string, integer or boolean): dates are normalized to an ISO date string,
     * arrays to their JSON representation.
     *
     * @param PersistentResource $resource
     * @param array<string, string|int|bool> $metaData
     * @return void
     * @throws ExtractorException
     */
    public function extractMetaData(PersistentResource $resource, array &$metaData): void;
}
