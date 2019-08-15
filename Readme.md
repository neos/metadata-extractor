[![StyleCI](https://styleci.io/repos/56771923/shield?branch=master)](https://styleci.io/repos/56771923)
[![Build Status](https://travis-ci.org/neos/metadata-extractor.svg?branch=master)](https://travis-ci.org/neos/metadata-extractor)
[![Latest Stable Version](https://poser.pugx.org/neos/metadata-extractor/v/stable)](https://packagist.org/packages/neos/metadata-extractor)
[![Total Downloads](https://poser.pugx.org/neos/metadata-extractor/downloads)](https://packagist.org/packages/neos/metadata-extractor)
[![License](https://poser.pugx.org/neos/metadata-extractor/license)](https://packagist.org/packages/neos/metadata-extractor)

# Neos.MetaData.Extractor Package

This package handles extraction of meta data from assets.

## Installation

Install using composer:

    composer require neos/metadata-extractor

Some related packages are:

- [`neos/metadata`](https://github.com/neos/metadata): Provides provides data types and interfaces
  (and is automatically installed with this package)
- [`neos/metadata-contentrepositoryadapter`](https://github.com/neos/metadata-contentrepositoryadapter):
  Handles the mapping of meta data DTOs to the Neos Content Repository

 ### Requirements

The package requires the `exif` PHP extension and uses the `iptcparse()` function (which
is available in PHP by default.)

## Configuration

This package provides realtime meta data extraction. This can be switched off, if needed, using:

```yaml
Neos:  
  MetaData:  
    Extractor:  
      realtimeExtraction:  
        enabled: false
```

## Usage  
  
The package extracts IPTC and EXIF meta data from assets using a CLI command:

    ./flow metadata:extract

When creating assets, the extraction is run as well (unless realtime extraction is disabled.)

The exact effect of the extraction depends on the implementation. If only this package is
installed and no further functionality has been implemented, the data supported by the
`AssetModelMetaDataMapper` from *Neos.MetaData* is stored in `Asset` models.

*In other words: You will get title, caption and copyright notice extracted and stored out
of the box.*

For developers, the package provides the `ExtractorInterface`. Using  `isSuitableFor()` the
implementing classes decide if they will be used for a specific resource. The `AbstractExtractor`
implements a check by media type. Just extend and set `$compatibleMediaTypes` to the possible
media type range(s). Returned DTOs are added to a collection and forwarded to the central
`MetaDataManger` of the *Neos.MetaData* package.

## Extractors  

The `ExtractionManager` itself generates the `Asset` DTO for every valid asset.

### `ExifExtractor` ([EXIF](http://www.exif.org/))
 
#### Supported Media Types 

* image/jpeg
* image/tiff
* video/jpeg

#### Generated DTOs

* EXIF

### `IptcIimExtractor` ([IPTC IIM](https://iptc.org/standards/iim/))

#### Supported Media Types

* application/octet-stream
* application/x-shockwave-flash
* image/bmp
* image/gif
* image/iff
* image/jp2
* image/jpeg
* image/png
* image/psd
* image/tiff
* image/vnd.microsoft.icon
* image/vnd.wap.wbmp
* image/xbm

#### Generated DTOs

* IPTC
