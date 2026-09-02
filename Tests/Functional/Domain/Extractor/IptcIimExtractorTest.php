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

use Neos\MetaData\Extractor\Domain\Extractor\IptcIimExtractor;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTestCase;

class IptcIimExtractorTest extends AbstractExtractorTestCase
{
    /**
     * @var IptcIimExtractor
     */
    protected $iptcIimExtractor;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->iptcIimExtractor = new IptcIimExtractor();
    }

    /**
     * @test
     */
    public function extractMetaData()
    {
        $extractedData = [];

        $this->iptcIimExtractor->extractMetaData($this->testAsset->getResource(), $extractedData);

        $expectedIptcData = [
            'iptc.City' => 'Otara',
            'iptc.CopyrightNotice' => '© Daniel Lienert',
            'iptc.Country' => 'Newzealand',
            'iptc.CountryCode' => 'NZ',
            'iptc.CreationDate' => '2013-09-18 10:59:11',
            'iptc.Creator' => '["Daniel Lienert"]',
            'iptc.CreatorTitle' => '["Informatiker"]',
            'iptc.CreditLine' => 'by-nc',
            'iptc.DeprecatedCategories' => '["Nature","Lig"]',
            'iptc.Description' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'iptc.DescriptionWriter' => '["Daniel Lienert"]',
            'iptc.DigitalCreationDate' => '2013-09-17 23:59:11',
            'iptc.Headline' => 'Waipapa Point Lighthouse',
            'iptc.Instructions' => 'None - it knows what to do',
            'iptc.JobId' => 'Shines in the night',
            'iptc.Keywords' => '["Beste","Leuchtturm","Neu Seeland","Neuseeland","New Zealand"]',
            'iptc.Source' => 'Camera',
            'iptc.State' => 'Southland',
            'iptc.Sublocation' => 'Waipapa Point Lighthouse',
            'iptc.Title' => 'Waipapa Point Leuchtturm',
        ];

        $this->assertExtractedData($expectedIptcData, $extractedData);
    }
}
