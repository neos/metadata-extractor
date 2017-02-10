# Neos Meta Data Extractor Package
This package handles extraction of meta data from assets. 

**Note: This package is work in progress. The class structure and interfaces may change a lot over time. The package is not meant for productive use.**

The package provides the `ExtractorInterface`. With `isSuitableFor()` the implementing classes decide if they will be used for a specific resource. The `AbstractExtractor` implements a check by media type. Just extend and set `$compatibleMediaTypes` to the possible media type range(s). Returned DTOs are added to a collection and forwarded to the central `MetaDataManger` of the package *Neos.MetaData*.

## Extractors
The `ExtractionManager` itself generates the `Asset` DTO for every valid Asset. 

### `ExifExtractor` ([EXIF](http://www.exif.org/))

#### Supported Media Types
* image/jpeg
* video/jpeg
* image/tiff

#### Generated DTOs
* EXIF

### `IptcIimExtractor` ([IPTC IIM](https://iptc.org/standards/iim/))

#### Supported Media Types
* image/gif
* image/jpeg
* image/png
* application/x-shockwave-flash
* image/psd
* image/bmp
* image/tiff
* application/octet-stream
* image/jp2
* image/iff
* image/vnd.wap.wbmp
* image/xbm
* image/vnd.microsoft.icon

#### Generated DTOs
* IPTC
