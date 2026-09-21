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

use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Media\Domain\Model\Asset;
use Neos\MetaData\Configuration\MetaDataConfigurationProviderYamlAdapter;
use Neos\MetaData\DimensionSpacePointProvider\DimensionSpacePointProvider;
use Neos\MetaData\Domain\Dto\MetaDataAssetReference;
use Neos\MetaData\Domain\Dto\MetaDataDimensionSpacePoint;
use Neos\MetaData\Domain\Dto\MetaDataDimensionSpacePoints;
use Neos\MetaData\Domain\Dto\MetaDataGlobalScope;
use Neos\MetaData\Domain\Dto\MetaDataPropertyName;
use Neos\MetaData\Domain\Dto\MetaDataPropertyNames;
use Neos\MetaData\Extractor\Domain\Dto\ExtractedMetaData;
use Neos\MetaData\MetaDataManager;
use Neos\MetaData\MetaDataManagerFactory;
use Neos\MetaData\Storage\MetaDataStorage;
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
     * In-memory metadata storage state shared by every {@see MetaDataManager} mock instance.
     *
     * Shared (instead of per instance) so that singletons which keep a reference to a previously
     * registered manager – e.g. the ExtractionManager – still read the values written by the current test.
     *
     * @var array<string, array<string, array<string, array<string, string|int|bool>>>>
     */
    protected static array $metaDataStoredValues = [];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->metaDataManager = $this->mockMetaDataManager();

        /** @var ResourceManager $resourceManager */
        $resourceManager = $this->objectManager->get(ResourceManager::class);
        $this->resourceManager = $resourceManager;
        $this->testAsset = $this->buildTestAsset();
        // the metadata storage references assets by their identifier, so the asset must be persisted first
        $this->persistenceManager->persistAll();
    }

    /**
     * Registers a MetaDataManager whose storage is an in-memory replacement, so that these functional
     * tests neither require the `neos_metadata_value` database table nor a MySQL database.
     *
     * The MetaDataManager itself is real – only its {@see MetaDataStorage} and
     * {@see DimensionSpacePointProvider} collaborators are replaced with in-memory implementations.
     * The property definitions are taken from the real configuration provider, so they match the
     * metadata properties configured by the installed packages.
     *
     * PHPUnit mocks would not survive across tests: their stubs are cleared by the framework after each
     * test, but the managers are kept alive beyond a test by singletons like the ExtractionManager.
     *
     * The factory is registered as singleton replacement, because the MetaDataManager itself is of
     * prototype scope and therefore cannot be replaced via setInstance.
     */
    protected function mockMetaDataManager(): MetaDataManager
    {
        $dimensionHashOf = static fn (MetaDataDimensionSpacePoint|MetaDataGlobalScope $scope): string =>
            $scope instanceof MetaDataGlobalScope ? 'global' : $scope->hash;

        self::$metaDataStoredValues = [];
        $storageState = &self::$metaDataStoredValues;

        $storage = new class($storageState, $dimensionHashOf) implements MetaDataStorage {
            /**
             * @var array<string, array<string, array<string, array<string, string|int|bool>>>>
             */
            private array $storageState;
            private \Closure $dimensionHashOf;

            /**
             * @param array<string, array<string, array<string, array<string, string|int|bool>>>> $storageState
             */
            public function __construct(array &$storageState, \Closure $dimensionHashOf)
            {
                $this->dimensionHashOf = $dimensionHashOf;
                $this->storageState = &$storageState;
            }

            public function setMetaDataPropertyValue(MetaDataAssetReference $assetReference, MetaDataPropertyName $propertyName, string|int|bool $propertyValue, MetaDataDimensionSpacePoint|MetaDataGlobalScope $scope): void
            {
                $this->storageState[$assetReference->assetSourceId][$assetReference->assetId][$propertyName->value][($this->dimensionHashOf)($scope)] = $propertyValue;
            }

            public function unsetMetaDataPropertyValue(MetaDataAssetReference $assetReference, MetaDataPropertyName $propertyName, MetaDataDimensionSpacePoint|MetaDataGlobalScope $scope): void
            {
                unset($this->storageState[$assetReference->assetSourceId][$assetReference->assetId][$propertyName->value][($this->dimensionHashOf)($scope)]);
            }

            public function getMetaDataPropertyValues(MetaDataAssetReference $assetReference, MetaDataPropertyName $propertyName, MetaDataDimensionSpacePoints|MetaDataGlobalScope $scope): array
            {
                $dimensionHashes = $scope instanceof MetaDataGlobalScope
                    ? ['global']
                    : $scope->map(static fn (MetaDataDimensionSpacePoint $dimensionSpacePoint) => $dimensionSpacePoint->hash);
                $propertyValues = $this->storageState[$assetReference->assetSourceId][$assetReference->assetId][$propertyName->value] ?? [];
                $result = [];
                foreach ($dimensionHashes as $dimensionHash) {
                    if (array_key_exists($dimensionHash, $propertyValues)) {
                        $result[$dimensionHash] = $propertyValues[$dimensionHash];
                    }
                }
                return $result;
            }

            public function findAssets(
                ?string $assetSourceId,
                ?string $searchTerm,
                MetaDataPropertyNames $localizedPropertyNames,
                MetaDataDimensionSpacePoints $dimensionSpacePointChain,
                MetaDataPropertyNames $globalScopePropertyNames,
            ): iterable {
                $localizedPropertyNames = $localizedPropertyNames->map(
                    static fn (MetaDataPropertyName $propertyName): string => $propertyName->value
                );
                $globalScopePropertyNames = $globalScopePropertyNames->map(
                    static fn (MetaDataPropertyName $propertyName): string => $propertyName->value
                );
                $searchTerm = $searchTerm === null ? null : strtolower($searchTerm);

                $references = [];
                foreach ($this->storageState as $storedAssetSourceId => $assetsByIdentifier) {
                    if ($assetSourceId !== null && $assetSourceId !== $storedAssetSourceId) {
                        continue;
                    }
                    foreach ($assetsByIdentifier as $assetId => $propertyValuesByName) {
                        foreach ($propertyValuesByName as $propertyName => $valuesByDimensionHash) {
                            $isGlobalScope = in_array($propertyName, $globalScopePropertyNames, true);
                            $isLocalized = !$isGlobalScope && in_array($propertyName, $localizedPropertyNames, true);
                            $candidateValues = [];
                            if ($isGlobalScope && array_key_exists('global', $valuesByDimensionHash)) {
                                $candidateValues[] = $valuesByDimensionHash['global'];
                            } elseif ($isLocalized) {
                                foreach ($dimensionSpacePointChain as $dimensionSpacePoint) {
                                    if (array_key_exists($dimensionSpacePoint->hash, $valuesByDimensionHash)) {
                                        $candidateValues[] = $valuesByDimensionHash[$dimensionSpacePoint->hash];
                                    }
                                }
                            }
                            foreach ($candidateValues as $candidateValue) {
                                if ($searchTerm === null
                                    || str_contains(strtolower((string)$candidateValue), $searchTerm)
                                ) {
                                    $references[] = MetaDataAssetReference::create($storedAssetSourceId, $assetId);
                                    break 3;
                                }
                            }
                        }
                    }
                }
                return $references;
            }
        };

        $emptyDimensionSpacePoint = MetaDataDimensionSpacePoint::fromCoordinates([]);
        $dimensionSpacePointProvider = new readonly class($emptyDimensionSpacePoint) implements DimensionSpacePointProvider {
            public function __construct(private readonly MetaDataDimensionSpacePoint $defaultDimensionSpacePoint)
            {
            }

            public function getDimensionSpacePoints(): MetaDataDimensionSpacePoints
            {
                return MetaDataDimensionSpacePoints::create($this->defaultDimensionSpacePoint);
            }

            public function getDefaultDimensionSpacePoint(): MetaDataDimensionSpacePoint
            {
                return $this->defaultDimensionSpacePoint;
            }

            public function getDimensionSpacePointChain(MetaDataDimensionSpacePoint $dimensionSpacePoint): MetaDataDimensionSpacePoints
            {
                return MetaDataDimensionSpacePoints::create($dimensionSpacePoint);
            }

            public function isDimensionSpacePointValid(MetaDataDimensionSpacePoint $dimensionSpacePoint): bool
            {
                return true;
            }
        };

        /** @var MetaDataConfigurationProviderYamlAdapter $configurationProvider */
        $configurationProvider = $this->objectManager->get(MetaDataConfigurationProviderYamlAdapter::class);
        $factory = new MetaDataManagerFactory(
            $storage,
            $dimensionSpacePointProvider,
            $configurationProvider,
        );
        $this->objectManager->setInstance(MetaDataManagerFactory::class, $factory);

        return $factory->create();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        self::$bootstrap->getObjectManager()->forgetInstance(MetaDataManagerFactory::class);
        parent::tearDown();
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
