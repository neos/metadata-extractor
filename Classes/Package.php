<?php
namespace Neos\MetaData\Extractor;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Flow\SignalSlot\Dispatcher as SignalSlotDispatcher;
use Neos\Media\Domain\Service\AssetService;
use Neos\MetaData\Extractor\Domain\ExtractionManager;

/**
 * @inheritDoc
 */
class Package extends BasePackage
{
    /**
     * @inheritDoc
     */
    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(AssetService::class, 'assetRemoved', ExtractionManager::class, 'onAssetRemoved');

        $packageKey = $this->getPackageKey();
        $dispatcher->connect(
            ConfigurationManager::class,
            'configurationManagerReady',
            function (ConfigurationManager $configurationManager) use ($packageKey, $dispatcher) {
                $extractionEnabled = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $packageKey . '.realtimeExtraction.enabled');
                if ($extractionEnabled === true) {
                    static::connectMetaDataExtraction($dispatcher);
                }
            }
        );
    }

    /**
     * @param SignalSlotDispatcher $dispatcher
     *
     * @return void
     */
    protected static function connectMetaDataExtraction(SignalSlotDispatcher $dispatcher)
    {
        $dispatcher->connect(AssetService::class, 'assetCreated', ExtractionManager::class, 'extractMetaData');
        $dispatcher->connect(AssetService::class, 'assetUpdated', ExtractionManager::class, 'extractMetaData');
    }
}
