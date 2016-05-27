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

abstract class AbstractExtractor implements ExtractorInterface
{

    /**
     * The media types this adapter can handle
     *
     * @var array
     */
    protected static $compatibleMediaTypes = [];


    /**
     * @return array
     * @throws \TYPO3\Neos\Exception
     */
    public static function getCompatibleMediaTypes()
    {
        if (!is_array(static::$compatibleMediaTypes)) {
            throw new \TYPO3\Neos\Exception('Identifier in class ' . __CLASS__ . ' is empty.', 1461246431);
        }

        return static::$compatibleMediaTypes;
    }
}