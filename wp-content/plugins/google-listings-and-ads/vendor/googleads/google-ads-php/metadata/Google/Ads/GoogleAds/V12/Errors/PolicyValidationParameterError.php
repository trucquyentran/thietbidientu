<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v12/errors/policy_validation_parameter_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V12\Errors;

class PolicyValidationParameterError
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�
Ggoogle/ads/googleads/v12/errors/policy_validation_parameter_error.protogoogle.ads.googleads.v12.errors"�
"PolicyValidationParameterErrorEnum"�
PolicyValidationParameterError
UNSPECIFIED 
UNKNOWN3
/UNSUPPORTED_AD_TYPE_FOR_IGNORABLE_POLICY_TOPICS8
4UNSUPPORTED_AD_TYPE_FOR_EXEMPT_POLICY_VIOLATION_KEYSL
HCANNOT_SET_BOTH_IGNORABLE_POLICY_TOPICS_AND_EXEMPT_POLICY_VIOLATION_KEYSB�
#com.google.ads.googleads.v12.errorsB#PolicyValidationParameterErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v12/errors;errors�GAA�Google.Ads.GoogleAds.V12.Errors�Google\\Ads\\GoogleAds\\V12\\Errors�#Google::Ads::GoogleAds::V12::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

