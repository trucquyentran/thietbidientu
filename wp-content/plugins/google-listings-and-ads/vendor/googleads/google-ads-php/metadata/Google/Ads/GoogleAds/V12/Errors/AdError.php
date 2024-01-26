<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v12/errors/ad_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V12\Errors;

class AdError
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
+
.google/ads/googleads/v12/errors/ad_error.protogoogle.ads.googleads.v12.errors"Ξ(
AdErrorEnum"Ύ(
AdError
UNSPECIFIED 
UNKNOWN,
(AD_CUSTOMIZERS_NOT_SUPPORTED_FOR_AD_TYPE
APPROXIMATELY_TOO_LONG
APPROXIMATELY_TOO_SHORT
BAD_SNIPPET
CANNOT_MODIFY_AD\'
#CANNOT_SET_BUSINESS_NAME_IF_URL_SET
CANNOT_SET_FIELD*
&CANNOT_SET_FIELD_WITH_ORIGIN_AD_ID_SET	/
+CANNOT_SET_FIELD_WITH_AD_ID_SET_FOR_SHARING
)
%CANNOT_SET_ALLOW_FLEXIBLE_COLOR_FALSE7
3CANNOT_SET_COLOR_CONTROL_WHEN_NATIVE_FORMAT_SETTING
CANNOT_SET_URL!
CANNOT_SET_WITHOUT_FINAL_URLS
CANNOT_SET_WITH_FINAL_URLS
CANNOT_SET_WITH_URL_DATA\'
#CANNOT_USE_AD_SUBCLASS_FOR_OPERATOR#
CUSTOMER_NOT_APPROVED_MOBILEADS(
$CUSTOMER_NOT_APPROVED_THIRDPARTY_ADS1
-CUSTOMER_NOT_APPROVED_THIRDPARTY_REDIRECT_ADS
CUSTOMER_NOT_ELIGIBLE1
-CUSTOMER_NOT_ELIGIBLE_FOR_UPDATING_BEACON_URL
DIMENSION_ALREADY_IN_UNION
DIMENSION_MUST_BE_SET
DIMENSION_NOT_IN_UNION#
DISPLAY_URL_CANNOT_BE_SPECIFIED 
DOMESTIC_PHONE_NUMBER_FORMAT
EMERGENCY_PHONE_NUMBER
EMPTY_FIELD0
,FEED_ATTRIBUTE_MUST_HAVE_MAPPING_FOR_TYPE_ID(
$FEED_ATTRIBUTE_MAPPING_TYPE_MISMATCH !
ILLEGAL_AD_CUSTOMIZER_TAG_USE!
ILLEGAL_TAG_USE"
INCONSISTENT_DIMENSIONS#)
%INCONSISTENT_STATUS_IN_TEMPLATE_UNION$
INCORRECT_LENGTH%
INELIGIBLE_FOR_UPGRADE&&
"INVALID_AD_ADDRESS_CAMPAIGN_TARGET\'
INVALID_AD_TYPE(\'
#INVALID_ATTRIBUTES_FOR_MOBILE_IMAGE)&
"INVALID_ATTRIBUTES_FOR_MOBILE_TEXT*
INVALID_CALL_TO_ACTION_TEXT+
INVALID_CHARACTER_FOR_URL,
INVALID_COUNTRY_CODE-*
&INVALID_EXPANDED_DYNAMIC_SEARCH_AD_TAG/
INVALID_INPUT0
INVALID_MARKUP_LANGUAGE1
INVALID_MOBILE_CARRIER2!
INVALID_MOBILE_CARRIER_TARGET3
INVALID_NUMBER_OF_ELEMENTS4
INVALID_PHONE_NUMBER_FORMAT51
-INVALID_RICH_MEDIA_CERTIFIED_VENDOR_FORMAT_ID6
INVALID_TEMPLATE_DATA7\'
#INVALID_TEMPLATE_ELEMENT_FIELD_TYPE8
INVALID_TEMPLATE_ID9
LINE_TOO_WIDE:!
MISSING_AD_CUSTOMIZER_MAPPING;
MISSING_ADDRESS_COMPONENT<
MISSING_ADVERTISEMENT_NAME=
MISSING_BUSINESS_NAME>
MISSING_DESCRIPTION1?
MISSING_DESCRIPTION2@
MISSING_DESTINATION_URL_TAGA 
MISSING_LANDING_PAGE_URL_TAGB
MISSING_DIMENSIONC
MISSING_DISPLAY_URLD
MISSING_HEADLINEE
MISSING_HEIGHTF
MISSING_IMAGEG-
)MISSING_MARKETING_IMAGE_OR_PRODUCT_VIDEOSH
MISSING_MARKUP_LANGUAGESI
MISSING_MOBILE_CARRIERJ
MISSING_PHONEK$
 MISSING_REQUIRED_TEMPLATE_FIELDSL 
MISSING_TEMPLATE_FIELD_VALUEM
MISSING_TEXTN
MISSING_VISIBLE_URLO
MISSING_WIDTHP\'
#MULTIPLE_DISTINCT_FEEDS_UNSUPPORTEDQ$
 MUST_USE_TEMP_AD_UNION_ID_ON_ADDR
TOO_LONGS
	TOO_SHORTT"
UNION_DIMENSIONS_CANNOT_CHANGEU
UNKNOWN_ADDRESS_COMPONENTV
UNKNOWN_FIELD_NAMEW
UNKNOWN_UNIQUE_NAMEX
UNSUPPORTED_DIMENSIONSY
URL_INVALID_SCHEMEZ 
URL_INVALID_TOP_LEVEL_DOMAIN[
URL_MALFORMED\\
URL_NO_HOST]
URL_NOT_EQUIVALENT^
URL_HOST_NAME_TOO_LONG_
URL_NO_SCHEME`
URL_NO_TOP_LEVEL_DOMAINa
URL_PATH_NOT_ALLOWEDb
URL_PORT_NOT_ALLOWEDc
URL_QUERY_NOT_ALLOWEDd4
0URL_SCHEME_BEFORE_EXPANDED_DYNAMIC_SEARCH_AD_TAGf)
%USER_DOES_NOT_HAVE_ACCESS_TO_TEMPLATEg$
 INCONSISTENT_EXPANDABLE_SETTINGSh
INVALID_FORMATi
INVALID_FIELD_TEXTj
ELEMENT_NOT_PRESENTk
IMAGE_ERRORl
VALUE_NOT_IN_RANGEm
FIELD_NOT_PRESENTn
ADDRESS_NOT_COMPLETEo
ADDRESS_INVALIDp
VIDEO_RETRIEVAL_ERRORq
AUDIO_ERRORr
INVALID_YOUTUBE_DISPLAY_URLs
TOO_MANY_PRODUCT_IMAGESt
TOO_MANY_PRODUCT_VIDEOSu.
*INCOMPATIBLE_AD_TYPE_AND_DEVICE_PREFERENCEv*
&CALLTRACKING_NOT_SUPPORTED_FOR_COUNTRYw-
)CARRIER_SPECIFIC_SHORT_NUMBER_NOT_ALLOWEDx
DISALLOWED_NUMBER_TYPEy*
&PHONE_NUMBER_NOT_SUPPORTED_FOR_COUNTRYz<
8PHONE_NUMBER_NOT_SUPPORTED_WITH_CALLTRACKING_FOR_COUNTRY{#
PREMIUM_RATE_NUMBER_NOT_ALLOWED|#
VANITY_PHONE_NUMBER_NOT_ALLOWED}#
INVALID_CALL_CONVERSION_TYPE_ID~=
9CANNOT_DISABLE_CALL_CONVERSION_AND_SET_CONVERSION_TYPE_ID#
CANNOT_SET_PATH2_WITHOUT_PATH13
.MISSING_DYNAMIC_SEARCH_ADS_SETTING_DOMAIN_NAME\'
"INCOMPATIBLE_WITH_RESTRICTION_TYPE1
,CUSTOMER_CONSENT_FOR_CALL_RECORDING_REQUIRED"
MISSING_IMAGE_OR_MEDIA_BUNDLE0
+PRODUCT_TYPE_NOT_SUPPORTED_IN_THIS_CAMPAIGN0
+PLACEHOLDER_CANNOT_HAVE_EMPTY_DEFAULT_VALUE=
8PLACEHOLDER_COUNTDOWN_FUNCTION_CANNOT_HAVE_DEFAULT_VALUE&
!PLACEHOLDER_DEFAULT_VALUE_MISSING)
$UNEXPECTED_PLACEHOLDER_DEFAULT_VALUE\'
"AD_CUSTOMIZERS_MAY_NOT_BE_ADJACENT,
\'UPDATING_AD_WITH_NO_ENABLED_ASSOCIATIONA
<CALL_AD_VERIFICATION_URL_FINAL_URL_DOES_NOT_HAVE_SAME_DOMAIN@
;CALL_AD_FINAL_URL_AND_VERIFICATION_URL_CANNOT_BOTH_BE_EMPTY
TOO_MANY_AD_CUSTOMIZERS!
INVALID_AD_CUSTOMIZER_FORMAT 
NESTED_AD_CUSTOMIZER_SYNTAX%
 UNSUPPORTED_AD_CUSTOMIZER_SYNTAX(
#UNPAIRED_BRACE_IN_AD_CUSTOMIZER_TAG,
\'MORE_THAN_ONE_COUNTDOWN_TAG_TYPE_EXISTS*
%DATE_TIME_IN_COUNTDOWN_TAG_IS_INVALID\'
"DATE_TIME_IN_COUNTDOWN_TAG_IS_PAST)
$UNRECOGNIZED_AD_CUSTOMIZER_TAG_FOUND(
#CUSTOMIZER_TYPE_FORBIDDEN_FOR_FIELD&
!INVALID_CUSTOMIZER_ATTRIBUTE_NAME
STORE_MISMATCH(
#MISSING_REQUIRED_IMAGE_ASPECT_RATIO
MISMATCHED_ASPECT_RATIOS*
%DUPLICATE_IMAGE_ACROSS_CAROUSEL_CARDSBμ
#com.google.ads.googleads.v12.errorsBAdErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v12/errors;errors’GAAͺGoogle.Ads.GoogleAds.V12.ErrorsΚGoogle\\Ads\\GoogleAds\\V12\\Errorsκ#Google::Ads::GoogleAds::V12::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

