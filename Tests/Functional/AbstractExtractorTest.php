<?php
namespace Neos\MetaData\Extractor\Tests\Functional;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Media\Tests\Functional\AbstractTest;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Flow\Utility\Files;


class AbstractExtractorTest extends AbstractTest
{

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var \TYPO3\Media\Domain\Model\Asset
     */
    protected $testAsset;


    public function setUp() {
        parent::setUp();

        $this->resourceManager = $this->objectManager->get(ResourceManager::class);
        $this->testAsset = $this->buildTestResource();
    }


    /**
     * @return \TYPO3\Flow\Resource\Resource
     * @throws \TYPO3\Flow\Resource\Exception
     */
    protected function buildTestResource() {
        $testImagePath = Files::concatenatePaths([__DIR__, 'Fixtures/Resources/Lighthouse.jpg']);
        $resource = $this->resourceManager->importResource($testImagePath);

        $asset = new \TYPO3\Media\Domain\Model\Asset($resource);
        
        return $asset;
    }
}