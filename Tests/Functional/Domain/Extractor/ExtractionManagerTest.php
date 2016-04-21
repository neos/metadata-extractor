<?php
namespace Neos\MetaData\Extractor\Tests\Functional\Domain\Extractor;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\ExtractionManager;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTest;

class ExtractionManagerTest extends AbstractExtractorTest
{

    /**
     * @var ExtractionManager
     */
    protected $extractionManager;


    public function setUp()
    {
        parent::setUp();

        $this->extractionManager = $this->objectManager->get(ExtractionManager::class);
    }
}