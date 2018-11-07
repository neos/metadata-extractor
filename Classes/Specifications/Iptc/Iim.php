<?php
namespace Neos\MetaData\Extractor\Specifications\Iptc;

/**
 * IPTC IIM
 *
 * @see https://www.iptc.org/std/IIM/4.2/specification/IIMV4.2.pdf Official Specification
 * @version 4.2
 */
class Iim
{
    // Envelope Record
    /**
     * Model Version
     *
     * Mandatory, not repeatable, two octets.
     *
     * A binary number identifying the version of the Information Interchange Model, Part I, utilised by the provider.
     * Version numbers are assigned by IPTC and NAA.
     */
    const MODEL_VERSION = '1#000';

    /**
     * Destination
     *
     * Optional, repeatable, maximum 1024 octets, consisting of sequentially contiguous graphic characters.
     *
     * This DataSet is to accommodate some providers who require routing information above the appropriate OSI layers.
     */
    const DESTINATION = '1#005';

    /**
     * File Format
     *
     * Mandatory, not repeatable, two octets.
     *
     * A binary number representing the file format. The file format must be registered with IPTC or NAA with a unique
     * number assigned to it (see Appendix A). The information is used to route the data to the appropriate system and
     * to allow the receiving system to perform the appropriate actions thereto.
     */
    const FILE_FORMAT = '1#020';

    /**
     * File Format Version
     *
     * Mandatory, not repeatable, two octets.
     *
     * A binary number representing the particular version of the File Format specified in 1:20.
     * A list of File Formats, including version cross references, is included as Appendix A.
     */
    const FILE_FORMAT_VERSION = '1#022';

    /**
     * Service Identifier
     *
     * Mandatory, not repeatable. Up to 10 octets, consisting of graphic characters.
     *
     * Identifies the provider and product.
     */
    const SERVICE_IDENTIFIER = '1#030';

    /**
     * Envelope Number
     *
     * Mandatory, not repeatable, eight octets, consisting of numeric characters.
     *
     * The characters form a number that will be unique for the date specified in 1:70 and for the Service Identifier
     * specified in 1:30.
     * If identical envelope numbers appear with the same date and with the same Service Identifier, records 2-9 must
     * be unchanged from the original. This is not intended to be a sequential serial number reception check.
     */
    const ENVELOPE_NUMBER = '1#040';

    /**
     * Product I.D.
     *
     * Optional, repeatable. Up to 32 octets, consisting of graphic characters.
     *
     * Allows a provider to identify subsets of its overall service. Used to provide receiving organisation data on
     * which to select, route, or otherwise handle data.
     */
    const PRODUCT_ID = '1#050';

    /**
     * Envelope Priority
     *
     * Optional, not repeatable. A single octet, consisting of a numeric character.
     *
     * Specifies the envelope handling priority and not the editorial urgency (see 2:10, Urgency).
     * '1' indicates the most urgent, '5' the normal urgency, and '8' the least urgent copy.
     * The numeral '9' indicates a User Defined Priority. The numeral '0' is reserved for future use.
     */
    const ENVELOPE_PRIORITY = '1#060';

    /**
     * Date Sent
     *
     * Mandatory, not repeatable. Eight octets, consisting of numeric characters.
     *
     * Uses the format CCYYMMDD (century, year, month, day) as defined in ISO 8601 to indicate year, month and day
     * the service sent the material.
     *
     * *Example*: An entry of `19890412` indicates data sent on 12 April 1989.
     */
    const DATE_SENT = '1#070';

    /**
     * Time Sent
     *
     * Optional, not repeatable, 11 octets, consisting of graphic characters.
     *
     * Uses the format HHMMSS±HHMM where HHMMSS refers to local hour, minute and seconds and HHMM refers to hours and
     * minutes ahead (+) or behind (-) Universal Coordinated Time as described in ISO 8601. This is the time the service
     * sent the material.
     *
     * *Example*: At 3:27 p.m. in New York in January it would be expressed as `152700-0500` as New York is five hours
     * behind UTC. At the same moment in Paris, the time would be expressed as `212700+0100`. In both instances the time
     * is 20:27 (8:27 p.m.) UTC. Midnight should be expressed as `240000` (with the appropriate offset from UTC).
     */
    const TIME_SENT = '1#080';

    /**
     * Coded Character Set
     *
     * Optional, not repeatable, up to 32 octets, consisting of one or more control functions used for the announcement,
     * invocation or designation of coded character sets. The control functions follow the ISO 2022 standard and may
     * consist of the escape control character and one or more graphic characters. For more details see Appendix C, the
     * IPTC-NAA Code Library.
     *
     * The control functions apply to character oriented DataSets in records 2-6. They also apply to record 8, unless
     * the objectdata explicitly, or the File Format implicitly, defines character sets otherwise.
     *
     * If this DataSet contains the designation function for Unicode in UTF-8 then no other announcement, designation or
     * invocation functions are permitted in this DataSet or in records 2-6.
     * For all other character sets, one or more escape sequences are used:
     * * for the announcement of the code extension facilities used in the data which follows,
     * * for the initial designation of the G0, G1, G2 and G3 graphic character sets and
     * * for the initial invocation of the graphic set (7 bits) or the lefthand and the right-hand graphic set (8 bits)
     *   and for the initial invocation of the C0 (7 bits) or of the C0 and the C1 control character sets (8 bits).
     *
     * The announcement of the code extension facilities, if transmitted, must appear in this data set. Designation and
     * invocation of graphic and control function sets (shifting) may be transmitted anywhere where the escape and the
     * other necessary control characters are permitted. However, it is recommended to transmit in this DataSet an
     * initial designation and invocation, i.e. to define all designations and the shift status currently in use by
     * transmitting the appropriate escape sequences and locking-shift functions.
     *
     * If 1:90 is omitted, the default for records 2-6 and 8 is ISO 646 IRV (7 bits) or ISO 4873 DV (8 bits). Record 1
     * shall always use ISO 646 IRV or ISO 4873 DV respectively.
     */
    const CODED_CHARACTER_SET = '1#090';

    /**
     * #UNO#
     *
     * Optional, not repeatable. Minimum of 14 and maximum of 80 octets consisting of graphic characters. Colon `:` and
     * solidus `/` are only allowed as specified, the asterisk `*` and question mark `?` are not allowed.
     *
     * UNO Unique Name of Object, providing eternal, globally unique identification for objects as specified in the IIM,
     * independent of provider and for any media form. The provider must ensure the UNO is unique. Objects with the same
     * UNO are identical.
     *
     * The UNO consists of four elements and provides the following functionality:
     * * **UNO Creation Date (UCD)**
     *
     *   Specifies a 24 hour period in which the further elements of the UNO have to be unique. It also provides a
     *   search facility.
     * * **Information Provider Reference (IPR)**
     *
     *   A name, registered with the IPTC/NAA, identifying the provider that guarantees the uniqueness of the UNO. It
     *   may assist in locating an object source.
     * * **Object Descriptor Element (ODE)**
     *
     *   In conjunction with the UCD and the IPR, a string of characters ensuring the uniqueness of the UNO. The
     *   provider may structure the element by use of a solidus `/` character.
     * * **Object Variant Indicator (OVI)**
     *
     *   A string of characters indicating technical variants of the object such as partial objects, or changes of file
     *   formats, and coded character sets.
     *
     * ##Rules##
     * The rules for the generation of the UNO are:
     * * The first three elements of the UNO (the UCD, the IPR and the ODE) together are allocated to the editorial
     *   content of the object.
     * * Any technical variants or changes in the presentation of an object, e.g. a picture being presented by a
     *   different file format, does not require the allocation of a new ODE but can be indicated by only generating a
     *   new OVI.
     *
     * ##Links##
     * Links may be set up to the complete UNO but the structure provides for linking to selected elements, e.g. to all
     * objects of a specified provider.
     *
     * ##UNO Component Definitions##
     * * **ES** (Element Separator)
     *
     *   Separates the elements within a UNO and consists of a single colon `:` character. All ES' are mandatory but
     * must not appear within an element.
     * * **ESD** (Element SubDivider)
     *
     *   Subdivides the ODE or OVI at the discretion of the provider and consists of a single solidus `/` character.
     * * **IPR** (Information Provider Reference)
     *
     *   Second element of the UNO. A minimum of one and a maximum of 32 octets. A string of graphic characters, except
     *   colon `:` solidus `/`, asterisk `*` and question mark `?`, registered with, and approved by, the IPTC.
     *   A list of registered strings of the IPR is located in Appendix E.
     * * **ODE** (Object Descriptor Element)
     *
     *   Third element of the UNO. A minimum of one and a maximum of 60 minus the number of IPR octets, consisting of
     *   graphic characters, except colon `:` asterisk `*` and question mark `?`. The provider bears the responsibility
     *   for the uniqueness of the ODE within a 24 hour cycle.
     * * **OVI** (Object Variant Indicator)
     *
     *   Fourth element of the UNO. A minimum of one and a maximum of 9 octets, consisting of graphic characters, except
     *   colon `:`, asterisk `*` and question mark `?`. To indicate a technical variation of the object as so far
     *   identified by the first three elements. Such variation may be required, for instance, for the indication of
     *   part of the object, or variations of the file format, or coded character set. The default value is a single
     *   `0` (zero) character indicating no further use of the OVI.
     * * **UCD** (UNO Creation Date)
     *
     *   First element of the UNO. 8 octets in ISO 8601 date format (CCYYMMDD), consisting of numeric characters.
     * * **UNO** (Unique Name of Object)
     *
     *   A universally unique name consisting of four elements. Total UNO has a minimum of 14 and maximum of 80 octets.
     *
     * ##UNO Structure##
     * ```
     * |                                 UNIQUE NAME of OBJECT (UNO)                                    |
     * |                       Minimum of 14 and maximum of 80 Octets for full UNO                      |
     * | ---------------------------------------------------------------------------------------------- |
     * |     UCD    | ES  |         IPR          | ES  |        ODE         | ES  |         OVI         |
     * | ---------- | --- | -------------------- | --- | ------------------ | --- | ------------------- |
     * |  CCYYMMDD  |  :  |    1 - 32 Octets     |  :  | Octets assigned by |  :  | Octets assigned by  |
     * | (ISO 8601) |     | registered with IPTC |     | Provider of Object |     | Provider of Object  |
     * | ---------- | --- | ----------------------------------------------- | --- | ------------------- |
     * |  8 Octets  |  1  |         Maximum of 61 Octets including ES       |  1  | Maximum of 9 Octets |
     * ```
     */
    const UNO = '1#100';

    /**
     * ARM Identifier
     *
     * Optional, not repeatable, two octets consisting of a binary number.
     *
     * The DataSet identifies the Abstract Relationship Method (ARM) which is described in a document registered by the
     * originator of the ARM with the IPTC and NAA.
     *
     * In Record 6, DataSets 6:192 through 6:255 are allocated for the purposes of the ARM.
     *
     * Details of the originator and a brief description of the ARM are contained in Appendix F.
     */
    const ARM_IDENTIFIER = '1#120';

    /**
     * ARM Version
     *
     * Mandatory if DataSet 1:120 is used, not repeatable, two octets consisting of a binary number representing the
     * particular version of the ARM specified in DataSet 1:120.
     *
     * A list of ARM Identifiers, including version cross references, is included as Appendix F.
     */
    const ARM_VERSION = '1#122';

    // Application Record
    /**
     * Record Version
     *
     * Mandatory, not repeatable, two octets.
     *
     * A binary number identifying the version of the Information Interchange Model, Part II (Record 2:xx), utilised by
     * the provider. Version numbers are assigned by IPTC and NAA.
     */
    const RECORD_VERSION = '2#000';

    /**
     * Object Type Reference
     *
     * Not repeatable, 3-67 octets, consisting of 2 numeric characters followed by a colon and an optional text part of
     * up to 64 octets.
     *
     * The Object Type is used to distinguish between different types of objects within the IIM.
     *
     * The first part is a number representing a language independentinternational reference to an Object Type followed
     * by a colon separator. The second part, if used, is a text representation of the Object Type Number (maximum 64
     * octets) consisting of graphic characters plus spaces either in English, as defined in Appendix G, or in the
     * language of the service as indicated in DataSet 2:135
     *
     * A list of Object Type Numbers and Names and their corresponding definitions will be maintained by the IPTC. See
     * Appendix G.
     *
     * ```
     * |                                Object Type Reference                                         |
     * | -------------------------------------------------------------------------------------------- |
     * |    Object Type Number     | ES  |                   Object Type Name                         |
     * | ------------------------- | --- | ---------------------------------------------------------- |
     * |  Two Octets assigned by   |     | 0 - 64 Octets for name associated to the number (if used)  |
     * | the IPTC as in Appendix G |  :  | as allocated by the IPTC or as translated by the provider  |
     * |                           |     |            in the language of the object.                  |
     * | ------------------------- | --- | ---------------------------------------------------------- |
     * |             2             |  1  |                          0 - 64                            |
     * | -------------------------------------------------------------------------------------------- |
     * |                               Minimum of 3, maximum of 67                                    |
     * ```
     */
    const OBJECT_TYPE_REFERENCE = '2#003';

    /**
     * Object Attribute Reference
     *
     * Repeatable, 4-68 octets, consisting of 3 numeric characters followed by a colon and an optional text part of up
     * to 64 octets.
     *
     * The Object Attribute defines the nature of the object independent of the Subject.
     *
     * The first part is a number representing a language independent international reference to an Object Attribute
     * followed by a colon separator. The second part, if used, is a text representation of the Object Attribute Number
     * (maximum 64 octets) consisting of graphic characters plus spaces either in English, as defined in Appendix G, or
     * in the language of the service as indicated in DataSet 2:135
     *
     * A registry of Object Attribute Numbers and Names and their corresponding definitions (if available) will be
     * maintained by the IPTC in different languages, with translations as supplied by members. See Appendix G.
     *
     * ```
     * |                            Object Attribute Reference                                        |
     * | -------------------------------------------------------------------------------------------- |
     * |  Object Attribute Number  | ES  |               Object Attribute Name                        |
     * | ------------------------- | --- | ---------------------------------------------------------- |
     * | Three Octets assigned by  |     | 0 - 64 Octets for name associated to the number (if used)  |
     * | the IPTC as in Appendix G |  :  | as allocated by the IPTC or as translated by the provider  |
     * |                           |     |            in the language of the object.                  |
     * | ------------------------- | --- | ---------------------------------------------------------- |
     * |             3             |  1  |                          0 - 64                            |
     * | -------------------------------------------------------------------------------------------- |
     * |                               Minimum of 4, maximum of 68                                    |
     * ```
     */
    const OBJECT_ATTRIBUTE_REFERENCE = '2#004';

    /**
     * Object Name
     *
     * Not repeatable, maximum 64 octets, consisting of graphic characters plus spaces.
     *
     * Used as a shorthand reference for the object. Changes to existing data, such as updated stories or new crops on
     * photos, should be identified in Edit Status.
     *
     * Examples: "Wall St.", "Ferry Sinks"
     */
    const OBJECT_NAME = '2#005';

    /**
     * Edit Status
     *
     * Not repeatable. Maximum 64 octets, consisting of graphic characters plus spaces.
     *
     *  Status of the objectdata, according to the practice of the provider.
     *
     * Examples: "Lead", "CORRECTION"
     */
    const EDIT_STATUS = '2#007';

    /**
     * Editorial Update
     *
     * Not repeatable, 2 octets, consisting of numeric characters.
     *
     * Indicates the type of update that this object provides to a previous object. The link to the previous object is
     * made using the ARM (DataSets 1:120 and 1:122), according to the practices of the provider.
     *
     * Possible values:
     *
     * * 01 Additional language.
     *
     *   Signifies that the accompanying Record 2 DataSets repeat information from another object in a different natural
     *   language (as indicated by DataSet 2:135).
     */
    const EDITORIAL_UPDATE = '2#008';

    /**
     * Urgency
     *
     * Not repeatable, one octet, consisting of a numeric character.
     *
     * Specifies the editorial urgency of content and not necessarily the envelope handling priority (see 1:60, Envelope
     * Priority). The '1' is most urgent, '5' normal and '8' denotes the least-urgent copy. The numerals '9' and '0' are
     * reserved for future use.
     */
    const URGENCY = '2#010';

    /**
     * Subject Reference
     *
     * Repeatable. Minimum of 13 and maximum of 236 octets consisting of graphic characters. Colon `:` is only allowed
     * as specified, the asterisk `*` and question mark `?` are not allowed, nor are the octet values 42 and 63.
     *
     * The character encoding used for this dataset must encode the colon `:` using octet value 58, and must not use
     * this octet value for any other purpose.
     *
     * The Subject Reference is a structured definition of the subject matter. It must contain an IPR (default value is
     * "IPTC"), an 8 digit Subject Reference Number and an optional Subject Name, Subject Matter Name and Subject Detail
     * Name. Each part of the Subject reference is separated by a colon (:). The Subject Reference Number contains three
     * parts, a 2 digit Subject Number, a 3 digit Subject Matter Number and a 3 digit Subject Detail Number thus
     * providing unique identification of the object's subject. If the Subject Matter or Subject Detail is not defined
     * then a value of 000 is used for the Subject Matter Number and/or Subject Detail Number as appropriate. (See
     * Appendices H and I). The DataSet may be repeated when the objectdata content is relevant to several subjects of
     * news interest. It can be independent of provider and for any media form. The provider must either use the IPTC
     * scheme or one that has been defined and published by the provider.
     *
     * The construction of the Subject Reference is as follows:
     * * **Information Provider Reference (IPR)**
     *
     *   A name, registered with the IPTC/NAA, identifying the provider that provides an indicator of the SDR content.
     *   The default value for the IPR is "IPTC" and is mandatory if the Subject Reference exists in the IPTC coding
     *   scheme as displayed in Appendices H - J.
     *
     * Individual registered Information Providers may at their discretion extend the Subject Reference lists. However,
     * they may only add to the subject matter and/or subject detail included in the IPTC lists, and must identify this
     * by using their registered IPR.The IPTC Subject list may not be extended.
     * * **Subject Reference Number**
     *
     *   Provides a numeric code to indicate the Subject Name plus optional Subject Matter and Subject Detail Names in
     *   the language of the service. Subject Reference Numbers consist of 8 octets in the range 01000000 to 17999999
     *   and represent a language independent international reference to a Subject. A Subject is identified by its
     *   Reference Number and corresponding Names taken from a standard lists given in Appendix H,I &J.These lists are
     *   the English language reference versions.
     *
     * * **Subject Name**
     *
     *   The third part, if used, is a text representation of the Subject Number (maximum 64 octets) consisting of
     *   graphic characters plus spaces either in English, as defined in Appendix H, or in the language of the service
     *   as indicated in DataSet 2:135 The Subject identifies the general content of the objectdata as determined by the
     *   provider.
     *
     * * **Subject Matter Name**
     *
     *   The fourth part, if used, is a text representation of the Subject Matter Number (maximum 64 octets) consisting
     *   of graphic characters plus spaces either in English, as defined in Appendix I, or in the language of the
     *   service as indicated in DataSet 2:135 A Subject Matter further refines the Subject of a News Object.
     *
     * * **Subject Detail Name**
     *
     *   The fifth part, if used, is a text representation of the Subject Detail Number (maximum 64 octets) consisting
     *   of graphic characters plus spaces either in English, as defined in Appendix J, or in the language of the
     *   service as indicated in DataSet 2:135
     *
     * A Subject Detail further refines the Subject Matter of a News Object. A registry of Subject Reference Numbers,
     * Subject Matter Names and Subject Detail Names, descriptions (if available) and their corresponding parent
     * Subjects will be held by the IPTC in different languages, with translations as supplied by members. See
     * Appendices I and J.
     *
     *
     * ```
     * |                 |     |       Subject       |     |   Subject   |     |    Subject     |     |    Subject     |
     * |       IPR       | ES  |      Reference      | ES  |    Name     | ES  |     Matter     | ES  |     Detail     |
     * |                 |     |       Number        |     |             |     |      Name      |     |      Name      |
     * | --------------- | --- | ------------------- | --- | ----------- | --- | -------------- | --- | -------------- |
     * |  1 - 32 Octets  |     |    Eight octets     |     | Maximum 64  |     |    Maximum     |     |    Maximum     |
     * | registered with |  :  |   assigned by the   |  :  |  octets to  |  :  |  64 octets to  |  :  |  64 octets to  |
     * |  IPTC for UNO   |     |  IPTC as contained  |     |  reference  |     | reference the  |     | reference the  |
     * |                 |     | in Appendices H,I,J |     | the Subject |     | Subject Matter |     | Subject Detail |
     * | --------------- | --- | ------------------- | --- | ----------- | --- | -------------- | --- | -------------- |
     * |   Minimum 1,    |  1  |          8          |  1  |    0-64     |  1  |      0-64      |  1  |      0-64      |
     * |  maximum of 32  |     |                     |     |   Octets    |     |     Octets     |     |     Octets     |
     * ```
     */
    const SUBJECT_REFERENCE = '2#012';

    /**
     * Category
     *
     * Not repeatable, maximum three octets, consisting of alphabetic characters.
     *
     * Identifies the subject of the objectdata in the opinion of the provider.
     *
     * A list of categories will be maintained by a regional registry, where available, otherwise by the provider.
     *
     * @deprecated use SUBJECT_REFERENCE
     */
    const CATEGORY = '2#015';

    /**
     * Supplemental Category
     *
     * Repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Supplemental categories further refine the subject of an objectdata. Only a single supplemental category may be
     * contained in each DataSet. A supplemental category may include any of the recognised categories as used in 2:15.
     * Otherwise, selection of supplemental categories are left to the provider.
     *
     * Examples: "NHL" (National Hockey League), "Fußball"
     *
     * @deprecated use SUBJECT_REFERENCE
     */
    const SUPPLEMENTAL_CATEGORY = '2#020';

    /**
     * Fixture Identifier
     *
     * Not repeatable, maximum 32 octets, consisting of graphic characters.
     *
     * Identifies objectdata that recurs often and predictably. Enables users to immediately find or recall such an
     * object.
     *
     * Example: "EUROWEATHER"
     */
    const FIXTURE_IDENTIFIER = '2#022';

    /**
     * Keywords
     *
     * Repeatable, maximum 64 octets, consisting of graphic characters plus spaces.
     *
     * Used to indicate specific information retrieval words.
     *
     * Each keyword uses a single Keywords DataSet. Multiple keywords use multiple Keywords DataSets. It is expected
     * that a provider of various types of data that are related in subject matter uses the same keyword, enabling the
     * receiving system or subsystems to search across all types of data for related material.
     *
     * Examples: "GRAND PRIX", "AUTO"
     */
    const KEYWORDS = '2#025';

    /**
     * Content Location Code
     *
     * Repeatable, 3 octets consisting of alphabetic characters.
     *
     * Indicates the code of a country/geographical location referenced by the content of the object.
     *
     * Where ISO has established an appropriate country code under ISO 3166, that code will be used. When ISO 3166 does
     * not adequately provide for identification of a location or a country, e.g. ships at sea, space, IPTC will assign
     * an appropriate three character code under the provisions of ISO 3166 to avoid conflicts (see Appendix D).
     * If used in the same object with DataSet 2:27, must immediately precede and correspond to it.
     */
    const CONTENT_LOCATION_CODE = '2#026';

    /**
     * Content Location Name
     *
     * Repeatable, maximum 64 octets, consisting of graphic characters plus spaces.
     *
     * Provides a full, publishable name of a country/geographical location referenced by the content of the object,
     * according to guidelines of the provider.
     *
     * If used in the same object with DataSet 2:26, must immediately follow and correspond to it.
     */
    const CONTENT_LOCATION_NAME = '2#027';

    /**
     * Release Date
     *
     * Not repeatable, eight octets, consisting of numeric characters.
     *
     * Designates in the form CCYYMMDD the earliest date the provider intends the object to be used. Follows ISO 8601
     * standard.
     *
     * Example: "19890317" indicates data for release on 17 March 1989.
     */
    const RELEASE_DATE = '2#030';

    /**
     * Release Time
     *
     * Not repeatable, 11 octets, consisting of graphic characters.
     *
     * Designates in the form HHMMSS±HHMM the earliest time the provider intends the object to be used. Follows ISO 8601
     * standard.
     *
     * Example: "090000-0500" indicates object for use after 0900 in New York (five hours behind UTC)
     */
    const RELEASE_TIME = '2#035';

    /**
     * Expiration Date
     *
     * Not repeatable, eight octets, consisting of numeric characters.
     *
     * Designates in the form CCYYMMDD the latest date the provider or owner intends the objectdata to be used. Follows
     * ISO 8601 standard.
     *
     * Example: “19940317” indicates an objectdata that should not be used after 17 March 1994.
     */
    const EXPIRATION_DATE = '2#037';

    /**
     * Expiration Time
     *
     * Not repeatable, 11 octets, consisting of graphic characters.
     *
     * Designates in the form HHMMSS±HHMM the latest time the provider or owner intends the objectdata to be used.
     * Follows ISO 8601 standard.
     *
     * Example: "090000-0500" indicates an objectdata that should not be used after 0900 in New York (five hours behind
     * UTC).
     */
    const EXPIRATION_TIME = '2#038';

    /**
     * Special Instructions
     *
     * Not repeatable, maximum 256 octets, consisting of graphic characters plus spaces.
     *
     * Other editorial instructions concerning the use of the objectdata, such as embargoes and warnings.
     *
     * Examples: "SECOND OF FOUR STORIES", "3 Pictures follow", "Argentina OUT"
     */
    const SPECIAL_INSTRUCTIONS = '2#040';

    /**
     * Action Advised
     *
     * Not repeatable, 2 octets, consisting of numeric characters.
     *
     * Indicates the type of action that this object provides to a previous object. The link to the previous object is
     * made using the ARM (DataSets 1:120 and 1:122), according to the practices of the provider.
     *
     * Possible values:
     * * **01** Object Kill
     *
     *   Signifies that the provider wishes the holder of a copy of the referenced object make no further use of that
     *   information and take steps to prevent further distribution thereof. Implies that any use of the object might
     *   result in embarrassment or other exposure of the provider and/or recipient.
     *
     * * **02** Object Replace
     *
     *   Signifies that the provider wants to replace the referenced object with the object provided under the current
     *   envelope.
     *
     * * **03** Object Append
     *
     *   Signifies that the provider wants to append to the referenced object information provided in the objectdata of
     *   the current envelope.
     *
     * * **04** Object Reference
     *
     *   Signifies that the provider wants to make reference to objectdata in a different envelope.
     */
    const ACTION_ADVISED = '2#042';

    /**
     * Reference Service
     *
     * Optional, repeatable, format identical with 1:30.
     *
     * Identifies the Service Identifier of a prior envelope to which the current object refers.
     *
     * Must be followed by 2:47 and 2:50 with repetition occurring in sequential triplets. Used together, 2:45, 2:47 and
     * 2:50 indicate that the current object refers to the content of a prior envelope.
     */
    const REFERENCE_SERVICE = '2#045';

    /**
     * Reference Date
     *
     * Mandatory if 2:45 exists and otherwise not allowed. Repeatable, format identical with 1:70
     *
     * Identifies the date of a prior envelope to which the current object refers.
     */
    const REFERENCE_DATE = '2#047';

    /**
     * Reference Number
     *
     * Mandatory if 2:45 exists and otherwise not allowed. Repeatable, format identical with 1:40.
     *
     * Identifies the Envelope Number of a prior envelope to which the current object refers.
     */
    const REFERENCE_NUMBER = '2#050';

    /**
     * Date Created
     *
     * Not repeatable, eight octets, consisting of numeric characters.
     *
     * Represented in the form CCYYMMDD to designate the date the intellectual content of the objectdata was created
     * rather than the date of the creation of the physical representation. Follows ISO 8601 standard. Where the month
     * or day cannot be determined, the information will be represented by “00”. Where the year cannot be determined,
     * the information for century and year will be represented by “00”.
     *
     * Thus a photo taken during the American Civil War would carry a creation date during that epoch (1861-1865) rather
     * than the date the photo was digitised for archiving.
     *
     * Example: "19900127" indicates the intellectual content created on 27th January 1990.
     */
    const DATE_CREATED = '2#055';

    /**
     * Time Created
     *
     * Not repeatable, 11 octets, consisting of graphic characters.
     *
     * Represented in the form HHMMSS±HHMM to designate the time the intellectual content of the objectdata current
     * source material was created rather than the creation of the physical representation. Follows ISO 8601 standard.
     *
     * Where the time cannot be precisely determined, the closest approximation should be used.
     *
     * Example: "133015+0100" indicates that the object intellectual content was created at 1:30 p.m. and 15 seconds
     * Frankfurt time, one hour ahead of UTC.
     */
    const TIME_CREATED = '2#060';

    /**
     * Digital Creation Date
     *
     * Not repeatable, eight octets, consisting of numeric characters.
     *
     * Represented in the form CCYYMMDD to designate the date the digital representation of the objectdata was created.
     * Follows ISO 8601 standard. Thus a photo taken during the American Civil War would carry a Digital Creation Date
     * within the past several years rather than the date where the image was captured on film, glass plate or other
     * substrate during that epoch (1861-1865).
     *
     * Example: "19900127" indicates digital form of the objectdata was created on 27th January 1990.
     */
    const DIGITAL_CREATION_DATE = '2#062';

    /**
     * Digital Creation Time
     *
     * Not repeatable, 11 octets, consisting of graphic characters.
     *
     * Represented in the form HHMMSS±HHMM to designate the time the digital representation of the objectdata was
     * created. Follows ISO 8601 standard.
     *
     * Example: "133015+0100" indicates that the digital form of the objectdata was created at 1:30 p.m. and 15 seconds
     * Frankfurt time, one hour ahead of UTC.
     */
    const DIGITAL_CREATION_TIME = '2#063';

    /**
     * Originating Program
     *
     * Not repeatable, maximum of 32 octets, consisting of graphic characters plus spaces.
     *
     * Identifies the type of program used to originate the objectdata.
     *
     * Examples: "Word Perfect", "SCITEX", "MacDraw"
     */
    const ORIGINATING_PROGRAM = '2#065';

    /**
     * Program Version
     *
     * Not repeatable, maximum of 10 octets, consisting of graphic characters plus spaces.
     *
     * Used to identify the version of the program mentioned in 2:65. DataSet 2:70 is invalid if 2:65 is not present.
     */
    const PROGRAM_VERSION = '2#070';

    /**
     * Object Cycle
     *
     * Not repeatable, one octet, consisting of an alphabetic character.
     *
     * Where:
     *
     * `a` = morning
     *
     * `p` = evening
     *
     * `b` = both
     *
     * Virtually only used in North America.
     */
    const OBJECT_CYCLE = '2#075';

    /**
     * By-line
     *
     * Repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Contains name of the creator of the objectdata, e.g. writer, photographer or graphic artist.
     *
     * Examples: "Robert Capa", "Ernest Hemingway", "Pablo Picasso"
     */
    const BYLINE = '2#080';

    /**
     * By-line Title
     *
     * Repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * A by-line title is the title of the creator or creators of an objectdata. Where used, a by-line title should
     * follow the by-line it modifies.
     *
     * Examples: "Staff Photographer", "Corresponsal", "Envoyé Spécial"
     */
    const BYLINE_TITLE = '2#085';

    /**
     * City
     *
     * Not repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Identifies city of objectdata origin according to guidelines established by the provider.
     *
     * Examples: "Zürich", "Milano", "New York"
     */
    const CITY = '2#090';

    /**
     * Sublocation
     *
     * Not repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Identifies the location within a city from which the objectdata originates, according to guidelines established
     * by the provider.
     *
     * Examples: "Capitol Hill", "Maple Leaf Gardens", "Strandgateparken"
     */
    const SUBLOCATION = '2#092';

    /**
     * Province/State
     *
     * Not repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Identifies Province/State of origin according to guidelines established by the provider.
     *
     * Examples: "WA", "Sussex", "Baden-Württenberg"
     */
    const PROVINCE_STATE = '2#095';

    /**
     * Country/Primary Location Code
     *
     * Not repeatable, three octets consisting of alphabetic characters.
     *
     * Indicates the code of the country/primary location where the intellectual property of the objectdata was created,
     * e.g. a photo was taken, an event occurred.
     *
     * Where ISO has established an appropriate country code under ISO 3166, that code will be used. When ISO 3166 does
     * not adequately provide for identification of a location or a new country, e.g. ships at sea, space, IPTC will
     * assign an appropriate three-character code under the provisions of ISO 3166 to avoid conflicts (see Appendix D).
     *
     * Examples: "USA" (United States), "FRA" (France), “XUN” (United Nations)
     */
    const COUNTRY_PRIMARY_LOCATION_CODE = '2#100';

    /**
     * Country/Primary Location Name
     *
     * Not repeatable, maximum 64 octets, consisting of graphic characters plus spaces.
     *
     * Provides full, publishable, name of the country/primary location where the intellectual property of the
     * objectdata was created, according to guidelines of the provider.
     */
    const COUNTRY_PRIMARY_LOCATION_NAME = '2#101';

    /**
     * Original Transmission Reference
     *
     * Not repeatable. Maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * A code representing the location of original transmission according to practices of the provider.
     *
     * Examples: BER-5, PAR-12-11-01
     */
    const ORIGINAL_TRANSMISSION_REFERENCE = '2#103';

    /**
     * Headline
     *
     * Not repeatable, maximum of 256 octets, consisting of graphic characters plus spaces.
     *
     * A publishable entry providing a synopsis of the contents of the objectdata.
     *
     * Example: "Lindbergh Lands In Paris"
     */
    const HEADLINE = '2#105';

    /**
     * Credit
     *
     * Not repeatable, maximum of 32 octets, consisting of graphic characters plus spaces.
     *
     * Identifies the provider of the objectdata, not necessarily the owner/creator.
     */
    const CREDIT = '2#110';

    /**
     * Source
     *
     * Not repeatable, maximum of 32 octets, consisting of graphic characters plus spaces.
     *
     * The name of a person or party who has a role in the content supply chain. This could be an agency, a member of an
     * agency, an individual or a combination. Source could be different from Creator and from the entities in the
     * Copyright Notice.
     */
    const SOURCE = '2#115';

    /**
     * Copyright Notice
     *
     * Not repeatable, maximum of 128 octets, consisting of graphic characters plus spaces.
     *
     * Contains any necessary copyright notice.
     */
    const COPYRIGHT_NOTICE = '2#116';

    /**
     * Contact
     *
     * Repeatable, maximum of 128 octets, consisting of graphic characters plus spaces.
     *
     * Identifies the person or organisation which can provide further background information on the objectdata.
     */
    const CONTACT = '2#118';

    /**
     * Caption/Abstract
     *
     * Not repeatable, maximum of 2000 octets, consisting of graphic characters plus carriage-returns, linefeeds and
     * spaces.
     *
     * A textual description of the objectdata, particularly used where the object is not text.
     */
    const CAPTION_ABSTRACT = '2#120';

    /**
     * Writer/Editor
     *
     * Repeatable, maximum 32 octets, consisting of graphic characters plus spaces.
     *
     * Identification of the name of the person involved in the writing, editing or correcting the objectdata or
     * caption/abstract.
     */
    const WRITER_EDITOR = '2#122';

    /**
     * Rasterized Caption
     *
     * Not repeatable, 7360 octets, consisting of binary data, one bit per pixel, two value bitmap where 1 (one)
     * represents black and 0 (zero) represents white.
     *
     * Image width 460 pixels and image height 128 pixels. Scanning direction bottom to top, left to right.
     *
     * Contains the rasterized objectdata description and is used where characters that have not been coded are required
     * for the caption.
     */
    const RASTERIZED_CAPTION = '2#125';

    /**
     * Image Type
     *
     * Not repeatable. Two octets. The first octet is a numeric character and the second is an alphabetic character.
     *
     * The numeric characters 1 to 4 indicate the number of components in an image, in single or multiple envelopes.
     *
     * The numeric character 0 indicates Record 2 caption for a specific image.
     *
     * The numeric character 9 specifies that the objectdata contains supplementary data to an image (as defined in the
     * Digital Newsphoto Parameter Record DataSet 3:55).
     *
     * ##Possible values##
     * ###Octet 1###
     * `0` = No objectdata. If this option is chosen, DataSet 8:10 of the objectdata Record will be present
     * (mandatory), but will be empty, i.e. a count of zero octets.
     *
     * `1` = Single component, e.g. black and white or one component of a colour project.
     *
     * `2`, `3`, `4` = Multiple components for a colour project.
     *
     * `9` = Supplemental objects related to other objectdata
     *
     * Other values are reserved for future use.
     *
     * The alphabetic character will indicate the exact content of the current objectdata in terms of colour
     * composition.
     * ###Octet 2###
     * `W` = Monochrome.
     *
     * `Y` = Yellow component.
     *
     * `M` = Magenta component.
     *
     * `C` = Cyan component.
     *
     * `K` = Black component.
     *
     * `R` = Red component.
     *
     * `G` = Green component.
     *
     * `B` = Blue component.
     *
     * `T` = Text only.
     *
     * `F` = Full colour composite, frame sequential.
     *
     * `L` = Full colour composite, line sequential.
     *
     * `P` = Full colour composite, pixel sequential.
     *
     * `S` = Full colour composite, special interleaving.
     *
     * Other values are reserved for future use.
     *
     * Note: When `0` or `T` are used, the only authorised combination is: `0T`
     */
    const IMAGE_TYPE = '2#130';

    /**
     * Image Orientation
     *
     * Not repeatable, one octet, consisting of an alphabetic character.
     * Allowed values are P (for Portrait), L (for Landscape) and S (for Square).
     *
     * Indicates the layout of the image area.
     */
    const IMAGE_ORIENTATION = '2#131';

    /**
     * Language Identifier
     *
     * Not repeatable, two or three octets, consisting of alphabetic characters.
     *
     * Describes the major national language of the object, according to the 2-letter codes of ISO 639:1988. Does not
     * define or imply any coded character set, but is used for internal routing, e.g. to various editorial desks.
     *
     * Implementation note: Programmers should provide for three octets for Language Identifier because the ISO is
     * expected to provide for 3-letter codes in the future.
     */
    const LANGUAGE_IDENTIFIER = '2#135';

    /**
     * Audio Type
     *
     * Not repeatable. Two octets. The first octet is a numeric character, while the second is an alphabetic character.
     *
     * Octet 1 represents the number of channels. Possible values:
     *
     * `0` = no objectdata.
     * If this option is chosen, DataSet 8:10 of the ObjectData Record will be present (It is mandatory.), but will be
     * empty, i.e. a count of zero octets.
     *
     * `1` = monaural (1 channel) audio
     *
     * `2`= stereo (2 channel) audio
     *
     * Other values are reserved for future use.
     *
     * Octet 2 indicates the exact type of audio contained in the current objectdata.
     *
     * Possible values:
     *
     * `A` = Actuality
     *
     * `C` = Question and answer session
     *
     * `M`= Music, transmitted by itself
     *
     * `Q` = Response to a question
     *
     * `R` = Raw sound
     *
     * `S` = Scener
     *
     * `T` = Text only
     *
     * `V` = Voicer
     *
     * `W` = Wrap
     *
     * Other values are reserved for future use.
     *
     * Examples:
     *
     * `1V` for a mono voicer
     *
     * `2M` for music recorded in stereo
     *
     * Note: When `0` or `T` is used, the only authorised combination is `0T`. This is the mechanism for sending a
     * caption either to supplement an audio cut sent previously without a caption or to correct a previously sent
     * caption.
     */
    const AUDIO_TYPE = '2#150';

    /**
     * Audio Sampling Rate
     *
     * Not repeatable. Six octets with leading zero(s), consisting of Sampling rate numeric characters, representing the
     * sampling rate in hertz (Hz).
     *
     * Examples: "011025" for a sample rate of 11025 Hz, "022050" for a sample rate of 22050 Hz, "044100" for a sample
     * rate of 44100 Hz
     */
    const AUDIO_SAMPLING_RATE = '2#151';

    /**
     * Audio Sampling Resolution
     *
     * Not repeatable. Two octets with leading zero(s), consisting of resolution numeric characters representing the
     * number of bits in each audio sample.
     *
     * Examples: "08" for a sample size of 8 bits, "16" for a sample size of 16 bits, "20" for a sample size of 20 bits
     */
    const AUDIO_SAMPLING_RESOLUTION = '2#152';

    /**
     * Audio Duration
     *
     * Not repeatable. Six octets, consisting of numeric characters.
     *
     * Duration Designates in the form HHMMSS the running time of an audio objectdata when played back at the speed at
     * which it was recorded.
     *
     * Example: "000105" for a cut lasting one minute, five seconds
     */
    const AUDIO_DURATION = '2#153';

    /**
     * Audio Outcue
     *
     * Not repeatable, maximum 64 octets, consisting of graphic characters plus spaces.
     *
     * Identifies the content of the end of an audio objectdata, according to guidelines established by the provider.
     *
     * Examples: "... better as a team", "fades", "...Jean Krause Paris"
     *
     * The outcue generally consists of the final words spoken within an audio objectdata or the final sounds heard.
     */
    const AUDIO_OUTCUE = '2#154';

    /**
     * ObjectData Preview File Format
     *
     * Mandatory if DataSet 2:202 exists; not repeatable, two octets.
     *
     * A binary number representing the file format of the ObjectData Preview. The file format must be registered with
     * IPTC or NAA with a unique number assigned to it.
     *
     * The values allowed are taken from the approved list of file formats registered for DataSet 1:20 and presented in
     * Appendix A.
     */
    const OBJECTDATA_PREVIEW_FILE_FORMAT = '2#200';

    /**
     * ObjectData Preview File Format Version
     *
     * Mandatory if DataSet 2:202 exists; not repeatable, two octets.
     *
     * A binary number representing the particular version of the ObjectData Preview File Format specified in 2:200
     *
     * The File Format Version is taken from the list included in Appendix A for DataSet 1:20 and 1:22.
     */
    const OBJECTDATA_PREVIEW_FILE_FORMAT_VERSION = '2#201';

    /**
     * ObjectData Preview Data
     *
     * Optional, not repeatable; maximum size of 256000 octets consisting of binary data.
     */
    const OBJECTDATA_PREVIEW_DATA = '2#202';

    // Pre-ObjectData Descriptor Record
    /**
     * Size Mode
     *
     * Mandatory, not repeatable, one octet.
     *
     * The octet is set to the binary value of '0' if the size of the objectdata is not known and is set to '1' if the
     * size of the objectdata is known at the beginning of transfer.
     */
    const SIZE_MODE = '7#010';

    /**
     * Max Subfile Size
     *
     * Mandatory, not repeatable.
     *
     * A binary number indicating the maximum size for the following Subfile DataSet(s).
     *
     * The largest number is not defined, but programmers should provide at least for the largest binary number
     * contained in four octets taken together. If the entire object is to be transferred together within a single
     * DataSet 8:10, the number equals the size of the object.
     */
    const MAX_SUBFILE_SIZE = '7#020';

    /**
     * ObjectData Size Announced
     *
     * Mandatory if DataSet 7:10 has value '1' and not allowed if DataSet 7:10 has value '0'. Not repeatable.
     *
     * A binary number representing the overall size of the objectdata, expressed in octets, not including tags, if that
     * size is known when transfer commences.
     */
    const OBJECTDATA_SIZE_ANNOUNCED = '7#090';

    /**
     * Maximum ObjectData Size
     *
     * Optional, not repeatable.
     *
     * A binary number used when objectdata size is not known, indicating the largest size, expressed in octets, that
     * the objectdata can possibly have, not including tags.
     */
    const MAXIMUM_OBJECTDATA_SIZE = '7#095';

    // ObjectData Record
    /**
     * Subfile
     *
     * Mandatory, repeatable.
     *
     * Subfile DataSet containing the objectdata itself. Subfiles must be sequential so that the subfiles may be
     * reassembled.
     */
    const SUBFILE = '8#010';

    // Post-ObjectData Descriptor Record
    /**
     * Confirmed ObjectData Size
     *
     * Mandatory, not repeatable.
     *
     * A binary number.
     *
     * Total size of the objectdata, in octets, without tags. This number should equal the number in DataSet 7:90 if the
     * size of the objectdata is known and has been provided.
     */
    const CONFIRMED_OBJECTDATA_SIZE = '9#010';

    /**
     * @var string[] Properties allowed to repeat themselves
     */
    public static $repeatable = [
        self::DESTINATION,
        self::PRODUCT_ID,
        self::OBJECT_ATTRIBUTE_REFERENCE,
        self::SUBJECT_REFERENCE,
        self::SUPPLEMENTAL_CATEGORY,
        self::KEYWORDS,
        self::CONTENT_LOCATION_CODE,
        self::CONTENT_LOCATION_NAME,
        self::REFERENCE_SERVICE,
        self::REFERENCE_DATE,
        self::REFERENCE_NUMBER,
        self::BYLINE,
        self::BYLINE_TITLE,
        self::CONTACT,
        self::WRITER_EDITOR,
        self::SUBFILE,
    ];

    /**
     * @var mixed[]
     */
    protected $properties;

    /**
     * IptcIim constructor.
     *
     * @param mixed[] $properties
     */
    public function __construct(array $properties)
    {
        // sometimes data is encoded in UTF-8 but not marked as such and therefore has to be converted
        if (!\array_key_exists(self::CODED_CHARACTER_SET, $properties)) {
            \array_walk_recursive(
                $properties,
                function (&$element) {
                    if (\is_string($element) && \mb_detect_encoding($element, 'UTF-8', true) === false) {
                        $element = \mb_convert_encoding($element, 'UTF-8', 'ISO-8859-1');
                    }
                }
            );
        }
        $this->properties = $properties;
    }

    /**
     * @param string $category
     * @return bool|string
     */
    public static function convertCategoryToSubjectCode(string $category)
    {
        $mapping = [
            'ACE' => '01000000',
            'CLJ' => '02000000',
            'DIS' => '03000000',
            'FIN' => '04000000',
            'EDU' => '05000000',
            'EVN' => '06000000',
            'HTH' => '07000000',
            'HUM' => '08000000',
            'LAB' => '09000000',
            'LIF' => '10000000',
            'POL' => '11000000',
            'REL' => '12000000',
            'SCI' => '13000000',
            'SOI' => '14000000',
            'SPO' => '15000000',
            'WAR' => '16000000',
            'WEA' => '17000000',
        ];

        return \array_key_exists($category, $mapping) ? $mapping[$category] : false;
    }

    /**
     * @param string $property
     * @return string|string[]
     */
    public function getProperty(string $property)
    {
        if (\array_key_exists($property, $this->properties)) {
            return \in_array($property, self::$repeatable, false)
                ? $this->properties[$property]
                : $this->properties[$property][0]
            ;
        }

        return \in_array($property, self::$repeatable, false) ? [] : '';
    }
}
