<?php
namespace Neos\MetaData\Extractor\Command;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\MetaData\Extractor\Domain\ExtractionManager;
use Neos\MetaData\Extractor\Exception\ExtractorException;

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
        }

        $this->outputLine("\nFinished extraction.");
    }
}
