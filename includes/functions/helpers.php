<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sanitize Google Analytics SiteID code
 *
 * Valid examples:
 * UA-..........
 * UA-..........-....
 *
 * @since 1.0.0
 * @param string $string
 * @return string
 */
function trackingcodesforwp_sanitize_ga_code($string) {
    preg_match('/UA-\d{4,10}(-\d{1,4})?/', $string, $matches);
    
    return isset($matches[0]) ? $matches[0] : '';
}

/**
 * Sanitize Google Tag Manager SiteID code
 *
 * Valid examples:
 * GTM-....
 *
 * @since 1.0.0
 * @param string $string
 * @return string
 */
function trackingcodesforwp_sanitize_gtm_code($string) {
    preg_match('/GTM(-[a-zA-Z0-9]{1,10})?/', $string, $matches);
    
    return isset($matches[0]) ? $matches[0] : '';
}

/**
 * Sanitize Google AdWords Remarketing code
 *
 * Valid examples:
 * <numbers>....
 *
 * @since 1.0.0
 * @param string $string
 * @return string
 */
function trackingcodesforwp_sanitize_gar_code($string) {
    preg_match('/([a-zA-Z0-9]{1,10})?/', $string, $matches);
    
    return isset($matches[0]) ? $matches[0] : '';
}
