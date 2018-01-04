<?php
namespace Neos\MetaData\Extractor\Command;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\MetaData\Extractor\Domain\ExtractionManager;
use Neos\MetaData\Extractor\Exception\ExtractorException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Repository\AssetRepository;

/**
 * @Flow\Scope("singleton")
 */
class MetaDataCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @Flow\Inject
     * @var ExtractionManager
     */
    protected $extractionManager;

    /**
     * @Flow\Inject
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Extracts MetaData from Assets
     */
    public function extractCommand()
    {
        $iterator = $this->assetRepository->findAllIterator();
        $assetCount = $this->assetRepository->countAll();

        $this->output->progressStart($assetCount);
        foreach ($this->assetRepository->iterate($iterator) as $asset) {
            /** @var Asset $asset */
            try {
                $this->extractionManager->extractMetaData($asset);
            } catch (ExtractorException $exception) {
                $this->output->outputLine(' ' . $exception->getMessage());
            }

            $this->output->progressAdvance(1);

            if($iterator->key() % 100 === 0) {
                $this->persistenceManager->persistAll();
            }
        }

        $this->persistenceManager->persistAll();
        $this->outputLine("\nFinished extraction.");
    }
}
