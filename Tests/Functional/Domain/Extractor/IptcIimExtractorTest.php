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

use Neos\MetaData\Domain\Collection\MetaDataCollection;
use Neos\MetaData\Domain\Dto;
use Neos\MetaData\Extractor\Domain\Extractor\IptcIimExtractor;
use Neos\MetaData\Extractor\Tests\Functional\AbstractExtractorTest;

class IptcIimExtractorTest extends AbstractExtractorTest
{
    /**
     * @var IptcIimExtractor
     */
    protected $iptcIimExtractor;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->iptcIimExtractor = new IptcIimExtractor();
    }

    /**
     * @test
     */
    public function extractMetaData()
    {
        $metaDataCollection = new MetaDataCollection();
        $this->iptcIimExtractor->extractMetaData($this->testAsset->getResource(), $metaDataCollection);
        $iptcDto = $metaDataCollection->get('iptc');

        $this->assertInstanceOf(Dto\Iptc::class, $iptcDto);

        $expectedIptcData = [
            'City' => 'Otara',
            'Contact' => [],
            'CopyrightNotice' => '© Daniel Lienert',
            'Country' => 'Newzealand',
            'CountryCode' => 'NZ',
            'CreationDate' => \DateTime::createFromFormat('YmdHis', '20130918105911'),
            'Creator' => ['Daniel Lienert'],
            'CreatorTitle' => ['Informatiker'],
            'CreditLine' => 'by-nc',
            'DeprecatedCategories' => ['Nature', 'Lig'],
            'Description' => 'Waipapa Point Lighthouse with the sea in the background and bush in the foreground.',
            'DescriptionWriter' => ['Daniel Lienert'],
            'DigitalCreationDate' => null,
            'Headline' => 'Waipapa Point Lighthouse',
            'Instructions' => 'None - it knows what to do',
            'IntellectualGenres' => [],
            'JobId' => 'Shines in the night',
            'Keywords' => [
                'Beste',
                'Leuchtturm',
                'Neu Seeland',
                'Neuseeland',
                'New Zealand'
            ],
            'Source' => 'Camera',
            'State' => 'Southland',
            'SubjectCodes' => [],
            'Sublocation' => 'Waipapa Point Lighthouse',
            'Title' => 'Waipapa Point Leuchtturm',
        ];

        $this->assertDtoGettersReturnData($iptcDto, $expectedIptcData);
    }
}
