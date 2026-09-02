<?php
namespace Neos\MetaData\Extractor\Tests\Functional\Domain\Extractor;

/*
 * This file is part of the Neos.MetaData.Extractor package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\MetaData\Domain\Dto\MetaDataPropertyName;
use Neos\MetaData\Extractor\Domain\ExtractionManager;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTestCase;

class ExtractionManagerTest extends AbstractExtractorTestCase
{
    /**
     * @var ExtractionManager
     */
    protected $extractionManager;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->extractionManager = $this->objectManager->get(ExtractionManager::class);
    }

    /**
     * @test
     */
    public function instanceCreated()
    {
        $this->assertInstanceOf(ExtractionManager::class, $this->extractionManager);
    }

    /**
     * @test
     */
    public function extractMetaData()
    {
        $extractedData = $this->extractionManager->extractMetaData($this->testAsset);

        $this->assertSame('Canon EOS 5D Mark II', $extractedData['exif.Model']);
        $this->assertSame('Otara', $extractedData['iptc.City']);

        // the values of the defined properties are persisted and can be read back via the MetaDataManager
        $this->assertSame('Canon EOS 5D Mark II', $this->getStoredMetaDataPropertyValue('exif.Model'));
        $this->assertSame('Daniel Lienert', $this->getStoredMetaDataPropertyValue('exif.Artist'));
        $this->assertSame('© Daniel Lienert', $this->getStoredMetaDataPropertyValue('iptc.CopyrightNotice'));
        $this->assertSame(
            '["Beste","Leuchtturm","Neu Seeland","Neuseeland","New Zealand"]',
            $this->getStoredMetaDataPropertyValue('iptc.Keywords')
        );

        // only properties that are defined in the meta data configuration are persisted
        $this->assertArrayHasKey('exif.MimeType', $extractedData);
        $this->assertSame('image/jpeg', $extractedData['exif.MimeType']);
        $this->assertFalse(
            $this->metaDataManager->getPropertyDefinitions()->include(MetaDataPropertyName::fromString('exif.MimeType'))
        );
    }
}
