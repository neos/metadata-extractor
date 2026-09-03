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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Media\Domain\Model\Asset;
use Neos\MetaData\Domain\Dto\MetaDataAssetReference;
use Neos\MetaData\Domain\Dto\MetaDataPropertyName;
use Neos\MetaData\Extractor\Domain\Dto\ExtractedMetaData;
use Neos\MetaData\MetaDataManager;
use Neos\Utility\Files;

abstract class AbstractExtractorTestCase extends FunctionalTestCase
{
    /**
     * @inheritDoc
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var Asset
     */
    protected $testAsset;

    /**
     * @var MetaDataManager
     */
    protected $metaDataManager;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->createMetaDataValueTable();

        /** @var ResourceManager $resourceManager */
        $resourceManager = $this->objectManager->get(ResourceManager::class);
        $this->resourceManager = $resourceManager;
        $this->testAsset = $this->buildTestAsset();
        // the metadata storage references assets by their identifier, so the asset must be persisted first
        $this->persistenceManager->persistAll();

        /** @var MetaDataManager $metaDataManager */
        $metaDataManager = $this->objectManager->get(MetaDataManager::class);
        $this->metaDataManager = $metaDataManager;
    }

    /**
     * Creates the table the MetaDataManager writes to.
     *
     * The table is not mapped as an entity, so the functional test schema, which is derived from entity
     * metadata only, does not contain it. As with the MetaData storage adapter tests, the foreign key of
     * the Doctrine migration is omitted on purpose - the migration cannot be run here.
     *
     * TODO: This should be solved in a better way in the metadata package so we can either mock the metadatamanager or can rely on the tables to exist
     *
     * @see \Neos\Flow\Persistence\Doctrine\Migrations\Version20260415145934
     */
    protected function createMetaDataValueTable(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->objectManager->get(EntityManagerInterface::class);
        $connection = $entityManager->getConnection();
        if (!$connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            $this->markTestSkipped('The metadata storage adapter requires MySQL or MariaDB');
        }
        $connection->executeStatement('CREATE TABLE IF NOT EXISTS neos_metadata_value (
            `asset_source_id` VARCHAR(255) DEFAULT NULL,
            `asset_id` VARCHAR(40) DEFAULT NULL,
            `property_name` VARCHAR(40) NOT NULL,
            `property_value` TEXT NOT NULL,
            `dimension_hash` VARCHAR(250) NOT NULL,
            UNIQUE INDEX idx_unique (`asset_source_id`, `asset_id`, `property_name`, `dimension_hash`)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    protected function buildTestAsset(): Asset
    {
        $testImagePath = Files::concatenatePaths([__DIR__, 'Fixtures/Resources/Lighthouse.jpg']);
        $this->assertFileExists($testImagePath);

        $resource = $this->resourceManager->importResource($testImagePath);

        return new Asset($resource);
    }

    /**
     * Asserts that the extracted data contains the given property values.
     *
     * @param array<string, mixed> $expectedExtractedData
     */
    protected function assertExtractedData(array $expectedExtractedData, ExtractedMetaData $extractedData): void
    {
        foreach ($expectedExtractedData as $propertyName => $expectedValue) {
            $this->assertTrue(
                $extractedData->has($propertyName),
                \sprintf('Extracted value for %s is missing.', $propertyName)
            );
            $this->assertEquals(
                $expectedValue,
                $extractedData->get($propertyName),
                \sprintf('Value of %s does not match expected.', $propertyName)
            );
        }
    }

    /**
     * The stored value of the given property of the test asset, as it is resolved by the MetaDataManager.
     */
    protected function getStoredMetaDataPropertyValue(string $propertyName): string|int|bool|null
    {
        return $this->metaDataManager->getMetaDataPropertyValue(
            $this->testAssetMetaDataReference(),
            MetaDataPropertyName::fromString($propertyName)
        )->value;
    }

    protected function testAssetMetaDataReference(): MetaDataAssetReference
    {
        return MetaDataAssetReference::create(
            $this->testAsset->getAssetSourceIdentifier(),
            $this->testAsset->getIdentifier()
        );
    }
}
