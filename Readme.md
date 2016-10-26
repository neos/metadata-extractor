# Neos Meta Data Extractor Package
This package handles extraction of meta data from assets. 

**Note: This package is work in progress. The class structure and interfaces may change a lot over time. The package is not meant for productive use.**

The package provides the `ExtractorInterface`. Implementing classes provide the compatible media types and are called with the target assets. Returned DTOs are added to a collection and forwarded to the central MetaDataManger class of package *Neos.MetaData*.

## Supported Media Types

Extractor                | Supported Media Types  | Generated DTOs
------------------------ | ---------------------- | --------------
ExtractionManager        | *                      | Asset
InterventionImageAdapter | image/jpeg, video/jpeg | EXIF, IPTC
