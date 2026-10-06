<?php

declare(strict_types=1);

namespace Neos\MetaData\Extractor\Domain\Dto;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use IteratorAggregate;
use Traversable;

/**
 * A collection of extracted metadata property values, keyed by prefixed property names
 * (e.g. {@code exif.Model}, {@code iptc.Title}).
 *
 * @implements IteratorAggregate<string, string|int|bool>
 */
final class ExtractedMetaData implements IteratorAggregate
{
    /**
     * @var array<string, string|int|bool>
     */
    private array $values = [];

    public function set(string $propertyName, string|int|bool $value): void
    {
        $this->values[$propertyName] = $value;
    }

    public function get(string $propertyName): string|int|bool|null
    {
        return $this->values[$propertyName] ?? null;
    }

    public function has(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->values);
    }

    /**
     * @return array<string, string|int|bool>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function getIterator(): Traversable
    {
        yield from $this->values;
    }
}
