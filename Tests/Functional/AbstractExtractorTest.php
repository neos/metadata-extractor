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

use Neos\MetaData\Domain\Dto\AbstractMetaDataDto;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Flow\Utility\Files;
use TYPO3\Media\Domain\Model\Asset;
use TYPO3\Media\Tests\Functional\AbstractTest;

/**
 * AbstractExtractor Test
 */
class AbstractExtractorTest extends AbstractTest
{
    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var Asset
     */
    protected $testAsset;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->resourceManager = $this->objectManager->get(ResourceManager::class);
        $this->testAsset = $this->buildTestResource();
    }

    /**
     * @return Asset
     */
    protected function buildTestResource()
    {
        $testImagePath = Files::concatenatePaths([
            __DIR__,
            'Fixtures/Resources/Lighthouse.jpg'
        ]);
        $resource = $this->resourceManager->importResource($testImagePath);

        return new Asset($resource);
    }

    /**
     * @param AbstractMetaDataDto $dto
     * @param array $expectedDtoData
     */
    protected function assertDtoGettersReturnData(AbstractMetaDataDto $dto, array $expectedDtoData)
    {
        foreach ($expectedDtoData as $key => $value) {
            $getter = 'get' . $key;
            $this->assertEquals($value, $dto->$getter());
        }
    }
}
