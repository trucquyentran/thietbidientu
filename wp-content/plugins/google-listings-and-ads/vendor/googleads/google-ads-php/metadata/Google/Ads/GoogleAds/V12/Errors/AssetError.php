<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v12/errors/asset_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V12\Errors;

class AssetError
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�
1google/ads/googleads/v12/errors/asset_error.protogoogle.ads.googleads.v12.errors"�
AssetErrorEnum"�


AssetError
UNSPECIFIED 
UNKNOWN,
(CUSTOMER_NOT_ON_ALLOWLIST_FOR_ASSET_TYPE
DUPLICATE_ASSET
DUPLICATE_ASSET_NAME
ASSET_DATA_IS_MISSING
CANNOT_MODIFY_ASSET_NAME&
"FIELD_INCOMPATIBLE_WITH_ASSET_TYPE
INVALID_CALL_TO_ACTION_TEXT(
$LEAD_FORM_INVALID_FIELDS_COMBINATION	
LEAD_FORM_MISSING_AGREEMENT

INVALID_ASSET_STATUS+
\'FIELD_CANNOT_BE_MODIFIED_FOR_ASSET_TYPE
SCHEDULES_CANNOT_OVERLAP9
5PROMOTION_CANNOT_SET_PERCENT_OFF_AND_MONEY_AMOUNT_OFF>
:PROMOTION_CANNOT_SET_PROMOTION_CODE_AND_ORDERS_OVER_AMOUNT%
!TOO_MANY_DECIMAL_PLACES_SPECIFIED/
+DUPLICATE_ASSETS_WITH_DIFFERENT_FIELD_VALUE2
.CALL_CARRIER_SPECIFIC_SHORT_NUMBER_NOT_ALLOWED5
1CALL_CUSTOMER_CONSENT_FOR_CALL_RECORDING_REQUIRED
CALL_DISALLOWED_NUMBER_TYPE"
CALL_INVALID_CONVERSION_ACTION
CALL_INVALID_COUNTRY_CODE-
)CALL_INVALID_DOMESTIC_PHONE_NUMBER_FORMAT
CALL_INVALID_PHONE_NUMBER/
+CALL_PHONE_NUMBER_NOT_SUPPORTED_FOR_COUNTRY(
$CALL_PREMIUM_RATE_NUMBER_NOT_ALLOWED(
$CALL_VANITY_PHONE_NUMBER_NOT_ALLOWED$
 PRICE_HEADER_SAME_AS_DESCRIPTION
MOBILE_APP_INVALID_APP_ID5
1MOBILE_APP_INVALID_FINAL_URL_FOR_APP_DOWNLOAD_URL 
NAME_REQUIRED_FOR_ASSET_TYPE 4
0LEAD_FORM_LEGACY_QUALIFYING_QUESTIONS_DISALLOWED! 
NAME_CONFLICT_FOR_ASSET_TYPE"
CANNOT_MODIFY_ASSET_SOURCE#-
)CANNOT_MODIFY_AUTOMATICALLY_CREATED_ASSET$B�
#com.google.ads.googleads.v12.errorsBAssetErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v12/errors;errors�GAA�Google.Ads.GoogleAds.V12.Errors�Google\\Ads\\GoogleAds\\V12\\Errors�#Google::Ads::GoogleAds::V12::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

