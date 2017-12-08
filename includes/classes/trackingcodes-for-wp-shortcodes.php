<?php
if ( ! defined ( 'ABSPATH') ) {
    exit;
}

if (! class_exists('Trackingcodes_for_WP_Shortcodes')) {
    Class Trackingcodes_for_WP_Shortcodes  {
        
        /**
         * Add shortcodes
         *
         * @since 1.0.0
         */
        public static function init() {
            $shortcodes = array(
                'print_tracking_code' => __CLASS__ . '::print_tracking_code'
            );
            
            foreach ($shortcodes as $shortcode => $method) {
                add_shortcode($shortcode, $method);
            }
        }
        
        /**
         * Shortcode Wrapper
         *
         * @since 1.0.0
         * @param string $function
         * @param array $atts
         * @param array $wrapper
         * @return string
         */
        public static function shortcode_wrapper($function, $atts = array(), $wrapper = array('before' => null, 'after' => null)) {
            ob_start();
            
            echo $wrapper['before'];
            call_user_func($function, $atts);
            echo $wrapper['after'];
            
            return ob_get_clean();
        }
        
        /**
         * Print Code shortcode.
         *
         * @since 1.0.0
         * @param array $atts
         * @return string
         */
        public static function print_tracking_code($atts) {
            return self::shortcode_wrapper(array('Trackingcodes_for_WP_Shortcodes_PrintTrackingCode', 'print_code'), $atts);
        }
        
    }
}

if (!class_exists('Trackingcodes_for_WP_Shortcodes_PrintTrackingCode')) {
    
    class Trackingcodes_for_WP_Shortcodes_PrintTrackingCode {
        
        protected $plugin_settings;
        
        public function __construct() {
            $this->plugin_settings = get_option('trackingcodesforwp_settings');
        }
        
        /**
         * Print Matracking
         *
         * @since 1.0.0
         * @param array $atts
         * @param string $content
         */
        public static function print_code($atts) {
            // Do not print if admin is logged in
            if (is_user_logged_in())
            {
                if (current_user_can( 'manage_options' ))
                {
                    return;
                }
            }
            
            $ga_code = trackingcodesforwp_sanitize_ga_code($this->plugin_settings['general']['ga_code']);
            $gtm_code = trackingcodesforwp_sanitize_gtm_code($this->plugin_settings['general']['gtm_code']);
            $gar_code = trackingcodesforwp_sanitize_gar_code($this->plugin_settings['general']['gar_code']);
            
            // show settings
            include_once(MATRACKING_VIEWS_PATH . 'google-analytics.php');
            include_once(MATRACKING_VIEWS_PATH . 'google-tag-manager-header.php');
            include_once(MATRACKING_VIEWS_PATH . 'google-tag-manager-body.php');
            include_once(MATRACKING_VIEWS_PATH . 'google-adwords-remarketing.php');
        }
        
    }
    
}
