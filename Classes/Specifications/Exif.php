<?php

namespace Neos\MetaData\Extractor\Specifications;

/**
 * EXIF
 *
 * @see http://www.cipa.jp/std/documents/e/DC-008-Translation-2016-E.pdf Official Specification PDF
 * @version 2.31
 */
class Exif
{
    public static $exifIfd = [
        // TIFF Rev. 6.0 Attribute Information
        0x100 => 'ImageWidth',
        0x101 => 'ImageLength',
        0x102 => 'BitsPerSample',
        0x103 => 'Compression',
        0x106 => 'PhotometricInterpretation',
        0x112 => 'Orientation',
        0x115 => 'SamplesPerPixel',
        0x11C => 'PlanarConfiguration',
        0x212 => 'YCbCrSubSampling',
        0x213 => 'YCbCrPositioning',
        0x11A => 'XResolution',
        0x11B => 'YResolution',
        0x128 => 'ResolutionUnit',
        0x111 => 'StripOffsets',
        0x116 => 'RowsPerStrip',
        0x117 => 'StripByteCounts',
        0x201 => 'JPEGInterchangeFormat',
        0x202 => 'JPEGInterchangeFormatLength',
        0x12D => 'TransferFunction',
        0x13E => 'WhitePoint',
        0x13F => 'PrimaryChromaticities',
        0x211 => 'YCbCrCoefficients',
        0x214 => 'ReferenceBlackWhite',
        0x132 => 'DateTime',
        0x10E => 'ImageDescription',
        0x10F => 'Make',
        0x110 => 'Model',
        0x131 => 'Software',
        0x13B => 'Artist',
        0x8298 => 'Copyright',
        // Exif IFD Attribute Information
        0x9000 => 'ExifVersion',
        0xA000 => 'FlashpixVersion',
        0xA001 => 'ColorSpace',
        0xA500 => 'Gamma',
        0x9101 => 'ComponentsConfiguration',
        0x9102 => 'CompressedBitsPerPixel',
        0xA002 => 'PixelXDimension',
        0xA003 => 'PixelYDimension',
        0x927C => 'MakerNote',
        0x9286 => 'UserComment',
        0xA004 => 'RelatedSoundFile',
        0x9003 => 'DateTimeOriginal',
        0x9004 => 'DateTimeDigitized',
        0x9010 => 'OffsetTime',
        0x9011 => 'OffsetTimeOriginal',
        0x9012 => 'OffsetTimeDigitized',
        0x9290 => 'SubSecTime',
        0x9291 => 'SubSecTimeOriginal',
        0x9292 => 'SubSecTimeDigitized',
        0x829A => 'ExposureTime',
        0x829D => 'FNumber',
        0x8822 => 'ExposureProgram',
        0x8824 => 'SpectralSensitivity',
        0x8827 => 'PhotographicSensitivity', // was 'ISOSpeedRatings' up to version 2.21
        0x8828 => 'OECF',
        0x8830 => 'SensitivityType',
        0x8831 => 'StandardOutputSensitivity',
        0x8832 => 'RecommendedExposureIndex',
        0x8833 => 'ISOSpeed',
        0x8834 => 'ISOSpeedLatitudeyyy',
        0x8835 => 'ISOSpeedLatitudezzz',
        0x9201 => 'ShutterSpeedValue',
        0x9202 => 'ApertureValue',
        0x9203 => 'BrightnessValue',
        0x9204 => 'ExposureBiasValue',
        0x9205 => 'MaxApertureValue',
        0x9206 => 'SubjectDistance',
        0x9207 => 'MeteringMode',
        0x9208 => 'LightSource',
        0x9209 => 'Flash',
        0x920A => 'FocalLength',
        0x9214 => 'SubjectArea',
        0xA20B => 'FlashEnergy',
        0xA20C => 'SpatialFrequencyResponse',
        0xA20E => 'FocalPlaneXResolution',
        0xA20F => 'FocalPlaneYResolution',
        0xA210 => 'FocalPlaneResolutionUnit',
        0xA214 => 'SubjectLocation',
        0xA215 => 'ExposureIndex',
        0xA217 => 'SensingMethod',
        0xA300 => 'FileSource',
        0xA301 => 'SceneType',
        0xA302 => 'CFAPattern',
        0xA401 => 'CustomRendered',
        0xA402 => 'ExposureMode',
        0xA403 => 'WhiteBalance',
        0xA404 => 'DigitalZoomRatio',
        0xA405 => 'FocalLengthIn35mmFilm',
        0xA406 => 'SceneCaptureType',
        0xA407 => 'GainControl',
        0xA408 => 'Contrast',
        0xA409 => 'Saturation',
        0xA40A => 'Sharpness',
        0xA40B => 'DeviceSettingDescription',
        0xA40C => 'SubjectDistanceRange',
        0x9400 => 'Temperature',
        0x9401 => 'Humidity',
        0x9402 => 'Pressure',
        0x9403 => 'WaterDepth',
        0x9404 => 'Acceleration',
        0x9405 => 'CameraElevationAngle',
        0xA420 => 'ImageUniqueID',
        0xA430 => 'CameraOwnerName',
        0xA431 => 'BodySerialNumber',
        0xA432 => 'LensSpecification',
        0xA433 => 'LensMake',
        0xA434 => 'LensModel',
        0xA435 => 'LensSerialNumber',
    ];

    public static $gpsIfd = [
        // GPS Attribute Information
        0x0 => 'GPSVersionID',
        0x1 => 'GPSLatitudeRef',
        0x2 => 'GPSLatitude',
        0x3 => 'GPSLongitudeRef',
        0x4 => 'GPSLongitude',
        0x5 => 'GPSAltitudeRef',
        0x6 => 'GPSAltitude',
        0x7 => 'GPSTimeStamp',
        0x8 => 'GPSSatellites',
        0x9 => 'GPSStatus',
        0xA => 'GPSMeasureMode',
        0xB => 'GPSDOP',
        0xC => 'GPSSpeedRef',
        0xD => 'GPSSpeed',
        0xE => 'GPSTrackRef',
        0xF => 'GPSTrack',
        0x10 => 'GPSImgDirectionRef',
        0x11 => 'GPSImgDirection',
        0x12 => 'GPSMapDatum',
        0x13 => 'GPSDestLatitudeRef',
        0x14 => 'GPSDestLatitude',
        0x15 => 'GPSDestLongitudeRef',
        0x16 => 'GPSDestLongitude',
        0x17 => 'GPSDestBearingRef',
        0x18 => 'GPSDestBearing',
        0x19 => 'GPSDestDistanceRef',
        0x1A => 'GPSDestDistance',
        0x1B => 'GPSProcessingMethod',
        0x1C => 'GPSAreaInformation',
        0x1D => 'GPSDateStamp',
        0x1E => 'GPSDifferential',
        0x1F => 'GPSHPositioningError',
    ];

    public static $valueInterpretationMap = [
        'Compression' => [
            1 => 'uncompressed',
            6 => 'JPEG compression (thumbnails only)',
            /** @see http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html */
            2 => 'CCITT 1D',
            3 => 'T4/Group 3 Fax',
            4 => 'T6/Group 4 Fax',
            5 => 'LZW',
            7 => 'JPEG',
            8 => 'Adobe Deflate',
            9 => 'JBIG B&W',
            10 => 'JBIG Color',
            99 => 'JPEG',
            262 => 'Kodak 262',
            32766 => 'Next',
            32767 => 'Sony ARW Compressed',
            32769 => 'Packed RAW',
            32770 => 'Samsung SRW Compressed',
            32771 => 'CCIRLEW',
            32772 => 'Samsung SRW Compressed 2',
            32773 => 'PackBits',
            32809 => 'Thunderscan',
            32867 => 'Kodak KDC Compressed',
            32895 => 'IT8CTPAD',
            32896 => 'IT8LW',
            32897 => 'IT8MP',
            32898 => 'IT8BL',
            32908 => 'PixarFilm',
            32909 => 'PixarLog',
            32946 => 'Deflate',
            32947 => 'DCS',
            34661 => 'JBIG',
            34676 => 'SGILog',
            34677 => 'SGILog24',
            34712 => 'JPEG 2000',
            34713 => 'Nikon NEF Compressed',
            34715 => 'JBIG2 TIFF FX',
            34718 => 'Microsoft Document Imaging (MDI) Binary Level Codec',
            34719 => 'Microsoft Document Imaging (MDI) Progressive Transform Codec',
            34720 => 'Microsoft Document Imaging (MDI) Vector',
            34892 => 'Lossy JPEG',
            65000 => 'Kodak DCR Compressed',
            65535 => 'Pentax PEF Compressed',
        ],
        'PhotometricInterpretation' => [
            2 => 'RGB',
            6 => 'YCbCr',
            /** @see http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html */
            0 => 'WhiteIsZero',
            1 => 'BlackIsZero',
            3 => 'RGB Palette',
            4 => 'Transparency Mask',
            5 => 'CMYK',
            8 => 'CIELab',
            9 => 'ICCLab',
            10 => 'ITULab',
            32803 => 'Color Filter Array',
            32844 => 'Pixar LogL',
            32845 => 'Pixar LogLuv',
            34892 => 'Linear Raw',
        ],
        'Orientation' => [
            1 => 'The 0th row is at the visual top of the image, and the 0th column is the visual left-hand side',
            2 => 'The 0th row is at the visual top of the image, and the 0th column is the visual right-hand side',
            3 => 'The 0th row is at the visual bottom of the image, and the 0th column is the visual right-hand side',
            4 => 'The 0th row is at the visual bottom of the image, and the 0th column is the visual left-hand side',
            5 => 'The 0th row is at the visual left-hand side of the image, and the 0th column is the visual top',
            6 => 'The 0th row is at the visual right-hand side of the image, and the 0th column is the visual top',
            7 => 'The 0th row is at the visual right-hand side of the image, and the 0th column is the visual bottom',
            8 => 'The 0th row is at the visual left-hand side of the image, and the 0th column is the visual bottom',
        ],
        'PlanarConfiguration' => [
            1 => 'chunky format',
            2 => 'planar format',
        ],
        'YCbCrPositioning' => [
            1 => 'centered',
            2 => 'co-sited',
        ],
        'ResolutionUnit' => [
            2 => 'inches',
            3 => 'centimeters',
            1 => 'None',
            4 => 'mm',
            5 => 'um',
        ],
        'FlashpixVersion' => [
            '0100' => 'Flashpix Format Version 1.0',
        ],
        'ColorSpace' => [
            1 => 'sRGB',
            0xFFFF => 'Uncalibrated',
            /** @see http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html */
            2 => 'Adobe RGB',
            0xFFFD => 'Wide Gamut RGB',
            0xFFFE => 'ICC Profile',
            // ?
            5 => 'CMYK',
            6 => 'YUV',
        ],
        'ExposureProgram' => [
            0 => 'Not defined',
            1 => 'Manual',
            2 => 'Normal program',
            3 => 'Aperture priority',
            4 => 'Shutter priority',
            5 => 'Creative program (biased toward depth of field)',
            6 => 'Action program (biased toward faster shutter speed)',
            7 => 'Portrait mode (for closeup photos with the background out of focus)',
            8 => 'Landscape mode (for landscape photos with the background in focus)',
            /** @see http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html */
            9 => 'Bulb',
        ],
        'SensitivityType' => [
            0 => 'Unknown',
            1 => 'Standard output sensitivity (SOS)',
            2 => 'Recommended exposure index (REI)',
            3 => 'ISO Speed',
            4 => 'Standard output sensitivity (SOS) and Recommended exposure index (REI)',
            5 => 'Standard output sensitivity (SOS) and ISO Speed',
            6 => 'Recommended exposure index (REI) and ISO Speed',
            7 => 'Standard output sensitivity (SOS) and Recommended exposure index (REI) and ISO Speed',
        ],
        'MeteringMode' => [
            0 => 'unknown',
            1 => 'Average',
            2 => 'CenterWeightedAverage',
            3 => 'Spot',
            4 => 'MultiSpot',
            5 => 'Pattern',
            6 => 'Partial',
            255 => 'other',
        ],
        'LightSource' => [
            0 => 'unknown',
            1 => 'Daylight',
            2 => 'Fluorescent',
            3 => 'Tungsten (incandescent light)',
            4 => 'Flash',
            9 => 'Fine weather',
            10 => 'Cloudy weather',
            11 => 'Shade',
            12 => 'Daylight fluorescent (D 5700 - 7100K)',
            13 => 'Day white fluorescent (N 4600 - 5500K)',
            14 => 'Cool white fluorescent (W 3800 - 4500K)',
            15 => 'White fluorescent (WW 3250 - 3800K)',
            16 => 'Warm white fluorescent (L 2600 - 3250K)',
            17 => 'Standard light A',
            18 => 'Standard light B',
            19 => 'Standard light C',
            20 => 'D55',
            21 => 'D65',
            22 => 'D75',
            23 => 'D50',
            24 => 'ISO studio tungsten',
            255 => 'other light source',
        ],
        'FocalPlaneResolutionUnit' => [
            2 => 'inches',
            3 => 'centimeters',
            /** @see http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html */
            1 => 'None',
            4 => 'mm',
            5 => 'um',
        ],
        'SensingMethod' => [
            1 => 'Not defined / Monochrome area',
            2 => 'One-chip color area sensor',
            3 => 'Two-chip color area sensor',
            4 => 'Three-chip color area sensor',
            5 => 'Color sequential area sensor',
            6 => 'Monochrome linear',
            7 => 'Trilinear sensor',
            8 => 'Color sequential linear sensor',
        ],
        'FileSource' => [
            0 => 'others',
            1 => 'scanner of transparent type',
            2 => 'scanner of reflex type',
            3 => 'DSC',
        ],
        'SceneType' => [
            1 => 'A directly photographed image',
        ],
        'CustomRendered' => [
            0 => 'Normal process',
            1 => 'Custom process',
        ],
        'ExposureMode' => [
            0 => 'Auto exposure',
            1 => 'Manual exposure',
            2 => 'Auto bracket',
        ],
        'WhiteBalance' => [
            0 => 'Auto white balance',
            1 => 'Manual white balance',
        ],
        'SceneCaptureType' => [
            0 => 'Standard',
            1 => 'Landscape',
            2 => 'Portrait',
            3 => 'Night scene',
        ],
        'GainControl' => [
            0 => 'None',
            1 => 'Low gain up',
            2 => 'High gain up',
            3 => 'Low gain down',
            4 => 'High gain down',
        ],
        'Contrast' => [
            0 => 'Normal',
            1 => 'Soft',
            2 => 'Hard',
        ],
        'Saturation' => [
            0 => 'Normal',
            1 => 'Low saturation',
            2 => 'High saturation',
        ],
        'Sharpness' => [
            0 => 'Normal',
            1 => 'Soft',
            2 => 'Hard',
        ],
        'SubjectDistanceRange' => [
            0 => 'unknown',
            1 => 'Macro',
            2 => 'Close view',
            3 => 'Distant view',
        ],
        'GPSLatitudeRef' => [
            'N' => 'North latitude',
            'S' => 'South latitude',
        ],
        'GPSLongitudeRef' => [
            'E' => 'East longitude',
            'W' => 'West longitude',
        ],
        'GPSAltitudeRef' => [
            0 => 'Sea level',
            1 => 'Sea level reference (negative value)',
        ],
        'GPSStatus' => [
            'A' => 'Measurement in progress',
            'V' => 'Measurement interrupted',
        ],
        'GPSMeasureMode' => [
            '2' => '2-dimensional measurement',
            '3' => '3-dimensional measurement',
        ],
        'GPSSpeedRef' => [
            'K' => 'Kilometers per hour',
            'M' => 'Miles per hour',
            'N' => 'Knots',
        ],
        'GPSTrackRef' => [
            'T' => 'True direction',
            'M' => 'Magnetic direction',
        ],
        'GPSImgDirectionRef' => [
            'T' => 'True direction',
            'M' => 'Magnetic direction',
        ],
        'GPSDestLatitudeRef' => [
            'N' => 'North latitude',
            'S' => 'South latitude',
        ],
        'GPSDestLongitudeRef' => [
            'E' => 'East longitude',
            'W' => 'West longitude',
        ],
        'GPSDestBearingRef' => [
            'T' => 'True direction',
            'M' => 'Magnetic direction',
        ],
        'GPSDestDistanceRef' => [
            'K' => 'Kilometers',
            'M' => 'Miles',
            'N' => 'Nautical miles',
        ],
        'GPSDifferential' => [
            0 => 'Measurement without differential correction',
            1 => 'Differential correction applied',
        ],
    ];

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return string|mixed
     */
    public static function interpretValue($property, $value)
    {
        switch ($property) {
            case 'DateTime':
            case 'DateTimeOriginal':
            case 'DateTimeDigitized':
                return \DateTime::createFromFormat('Y:m:d H:i:s', $value);
            case 'YCbCrSubSampling':
                if (isset($value[0], $value[1]) && $value[0] === 2) {
                    switch ($value[1]) {
                        case 1:
                            return 'YCbCr4:2:2';
                        case 2:
                            return 'YCbCr4:2:0';
                    }
                }

                return $value;
            case 'ComponentsConfiguration':
                $interpretedValue = '';
                $componentsConfigurationInterpretations = [
                    0 => '', // does not exist
                    1 => 'Y',
                    2 => 'Cb',
                    3 => 'Cr',
                    4 => 'R',
                    5 => 'G',
                    6 => 'B',
                ];
                foreach (unpack('C*', $value) as $singleValue) {
                    if (isset($componentsConfigurationInterpretations[$singleValue])) {
                        $interpretedValue .= $componentsConfigurationInterpretations[$singleValue];
                    }
                }

                return $interpretedValue;
            case 'Flash':
                $interpretedValue = '';
                $flashFired = $value & 0b1;
                $flashReturn = ($value >> 1) & 0b11;
                $flashMode = ($value >> 3) & 0b11;
                $flashFunction = ($value >> 5) & 0b1;
                $redEyeMode = ($value >> 6) & 0b1;
                $interpretedValue .= ($flashFired === 0b1) ? 'Flash fired.' : 'Flash did not fire.';
                switch ($flashReturn) {
                    case 0b00:
                        $interpretedValue .= ' No strobe return detection function.';
                        break;
                    case 0b10:
                        $interpretedValue .= ' Strobe return light not detected.';
                        break;
                    case 0b11:
                        $interpretedValue .= ' Strobe return light detected.';
                        break;
                }
                switch ($flashMode) {
                    case 0b00:
                        $interpretedValue .= ' Flash mode unknown.';
                        break;
                    case 0b01:
                        $interpretedValue .= ' Compulsory flash firing.';
                        break;
                    case 0b10:
                        $interpretedValue .= ' Compulsory flash suppression.';
                        break;
                    case 0b11:
                        $interpretedValue .= ' Auto flash mode.';
                        break;
                }
                $interpretedValue .= ($flashFunction === 0b1) ? ' No flash function.' : ' Flash function present.';
                $interpretedValue .= ($redEyeMode === 0b1) ? ' Red-eye reduction supported.' : ' No red-eye reduction mode or unknown.';

                return $interpretedValue;
        }
        if (isset(static::$valueInterpretationMap[$property][$value])) {
            return static::$valueInterpretationMap[$property][$value];
        }

        return $value;
    }
}
