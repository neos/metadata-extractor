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
use Neos\Flow\Core\Booting\Sequence;
use Neos\Flow\Core\Booting\Step;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Media\Domain\Model\Asset;
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
        $dispatcher->connect(AssetService::class, 'assetRemoved', ExtractionManager::class, 'extractMetaData');
        $dispatcher->connect(AssetService::class, 'assetResourceReplaced', ExtractionManager::class, 'extractMetaData');
        $package = $this;
        $dispatcher->connect(
            Sequence::class,
            'afterInvokeStep',
            function (Step $step) use ($package, $bootstrap) {
                if ($step->getIdentifier() === 'neos.flow:reflectionservice') {
                    $package->registerExtractionSlot($bootstrap);
                }
            }
        );
    }

    /**
     * Registers slots for signals in order to be able to index nodes
     *
     * @param Bootstrap $bootstrap
     */
    public function registerExtractionSlot(Bootstrap $bootstrap)
    {
        $configurationManager = $bootstrap->getObjectManager()->get(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->getPackageKey());

        if (isset($settings['realtimeExtraction']['enabled']) && $settings['realtimeExtraction']['enabled'] === true) {
            $dispatcher = $bootstrap->getSignalSlotDispatcher();
            $dispatcher->connect(AssetService::class, 'assetCreated', ExtractionManager::class, 'extractMetaData');
        }
    }
}
