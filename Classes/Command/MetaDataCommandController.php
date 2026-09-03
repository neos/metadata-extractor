<?php

declare(strict_types=1);
namespace Neos\MetaData\Extractor\Command;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\MetaData\Extractor\Domain\ExtractionManager;

#[Flow\Scope('singleton')]
class MetaDataCommandController extends CommandController
{
    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected ExtractionManager $extractionManager;

    #[Flow\Inject]
    protected PersistenceManager $persistenceManager;

    /**
     * Extracts MetaData from Assets
     */
    public function extractCommand(): void
    {
        $iterator = $this->assetRepository->findAllIterator();
        $assetCount = $this->assetRepository->countAll();

        if ($assetCount === 0) {
            $this->output->outputLine('No assets found.');
            return;
        }

        $this->output->progressStart($assetCount);
        /** @phpstan-ignore-next-line */
        foreach ($this->assetRepository->iterate($iterator) as $asset) {
            /** @var Asset $asset */
            $this->extractionManager->extractMetaData($asset);

            $this->output->progressAdvance(1);

            if ($iterator->key() % 100 === 0) {
                $this->persistenceManager->persistAll();
            }
        }

        $this->persistenceManager->persistAll();
        $this->outputLine("\nFinished extraction.");
    }
}
