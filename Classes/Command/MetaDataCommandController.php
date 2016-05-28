<?php
namespace Neos\MetaData\Extractor\Command;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 */

use Neos\MetaData\Extractor\Exception\NoExtractorAvailableException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Repository\AssetRepository;

/**
 * @Flow\Scope("singleton")
 */
class MetaDataCommandController extends \TYPO3\Flow\Cli\CommandController
{

    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @Flow\Inject
     * @var \Neos\MetaData\Extractor\Domain\ExtractionManager
     */
    protected $extractionManager;

    
    public function extractCommand()
    {
        $iterator = $this->assetRepository->findAllIterator();
        $assetCount = $this->assetRepository->countAll();

        $this->output->progressStart($assetCount);
        foreach ($this->assetRepository->iterate($iterator) as $asset) {
            /** @var \TYPO3\Media\Domain\Model\Document $asset */

            try {
                $this->extractionManager->extractMetaData($asset);
            } catch (NoExtractorAvailableException $exception) {
                $this->output->outputLine($exception->getMessage());
            }

            $this->output->progressAdvance(1);
        }

        $this->outputLine("\nFinished extraction.");
    }
}
