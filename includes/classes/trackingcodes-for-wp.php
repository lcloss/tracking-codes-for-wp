<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if (! class_exists(TRACKINGCODESWP_CLASS_NAME)) {
    
    Class Trackingcodes_for_WP {
        
        // Current Version
        const VERSION = TRACKINGCODESWP_PLUGIN_VERSION;
        
        protected $plugin_slug = TRACKINGCODESWP_PLUGIN_SLUG;
        protected $plugin_settings;
        protected $plugin_basename;
        protected static $instance = null;
        
        private function __construct() {
            $this->plugin_settings = get_option('trackingcodesforwp_settings');
            $this->plugin_basename = plugin_basename(TRACKINGCODESWP_PATH . TRACKINGCODESWP_PLUGIN_SLUG . '.php');
            
            // Load plugin text domain
            add_action('init', array($this, 'load_plugin_textdomain'));
            
            // Add shortcodes
            add_action('init', array('Trackingcodes_for_WP_Shortcodes', 'init'));
            
            // Check update
            add_action('admin_init', array($this, 'check_update'));
            
            // INIT
            add_action('init', array($this, 'init'));
            add_action('wp_head', array($this, 'google_analytics_code'), 100);
            add_action('wp_head', array($this, 'google_tag_manager_code_header'), 100);
            // Disable twice tag: enable on wp_head or wp_footer
            // add_action('wp_footer', array($this, 'google_tag_manager_code_body'), 5);
            add_action('wp_footer', array($this, 'google_adwords_remarketing'), 100);
        }
        
        /**
         * What to do when the plugin is activated
         *
         * @since 1.0.0
         * @param boolean $network_wide
         */
        public static function activate($network_wide) {
            // because we need translated items when activate :)
            load_plugin_textdomain(self::get_instance()->plugin_slug, FALSE, TRACKINGCODESWP_LANGUAGES_PATH);
            
            // do the job
            if (function_exists('is_multisite') && is_multisite()) {
                if ($network_wide) {
                    // Get all blog ids
                    $blog_ids = self::get_blog_ids();
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        self::single_activate($network_wide);
                        restore_current_blog();
                    }
                } else {
                    self::single_activate();
                }
            } else {
                self::single_activate();
            }
        }
        
        /**
         * Check plugin version for updating process
         *
         * @since 1.0.0
         */
        public function check_update() {
            /*
             $version = get_option('trackingcodesforwp_version', '0');
             
             if (!version_compare($version, Trackingcodes_for_WP::VERSION, '=')) {
             self::activate(is_multisite() && is_plugin_active_for_network($this->plugin_basename) ? true : false);
             }
             */
            // Do nothing for now
        }
        
        /**
         * What to do when the plugin is deactivated
         *
         * @since 1.0.0
         * @param boolean $network_wide
         */
        public static function deactivate($network_wide) {
            if (function_exists('is_multisite') && is_multisite()) {
                if ($network_wide) {
                    // Get all blog ids
                    $blog_ids = self::get_blog_ids();
                    foreach ($blog_ids as $blog_id) {
                        switch_to_blog($blog_id);
                        self::single_deactivate();
                        restore_current_blog();
                    }
                } else {
                    self::single_deactivate();
                }
            } else {
                self::single_deactivate();
            }
        }
        
        
        /**
         * What to do on single activate
         *
         * @since 1.0.0
         * @global object $wpdb
         * @param boolean $network_wide
         */
        public static function single_activate($network_wide = false) {
            global $wpdb;
            
            // create tables
            // get all options of the plugin
            $options = get_option('trackingcodesforwp_settings');
            $default_options = self::get_instance()->default_settings();
            
            if (empty($options)) {
                // set options
                add_option('trackingcodesforwp_settings', $default_options);
            }
            
            // set current version
            update_option('trackingcodesforwp_version', Trackingcodes_for_WP::VERSION);
        }
        
        /**
         * What to do on single deactivate
         *
         * @since 1.0.0
         */
        public static function single_deactivate() {
            // nothing
        }
        
        /**
         * Return plugin default settings
         *
         * @since 2.0.0
         * @return array
         */
        public function default_settings() {
            return array(
                'general' => array(
                    'ga_code' => ''
                    ,	'gtm_code' => ''
                    ,	'gar_code'	=> ''
                )
            );
        }
        
        /**
         * Get Instance
         */
        public static function get_instance() {
            if (null == self::$instance) {
                self::$instance = new self;
            }
            
            return self::$instance;
        }
        
        /**
         * Get all blog ids of blogs in the current network
         *
         * @since 1.0.0
         * @return array / false
         */
        private static function get_blog_ids() {
            global $wpdb;
            
            return $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE archived = %d AND spam = %d AND deleted = %d", array(0, 0, 0)));
        }
        
        /**
         * Return plugin slug
         *
         * @since 1.0.0
         * @return string
         */
        public function get_plugin_slug() {
            return $this->plugin_slug;
        }
        
        /**
         * Return plugin settings
         *
         * @since 1.0.0
         * @return array
         */
        public function get_plugin_settings() {
            return $this->plugin_settings;
        }
        
        /**
         * Google Analytics code
         *
         * @since 1.0.0
         */
        public function google_analytics_code() {
            // check if module is activated and code exists
            if (empty($this->plugin_settings['general']['ga_code'])) {
                return false;
            }
            
            // sanitize code
            $ga_code = trackingcodesforwp_sanitize_ga_code($this->plugin_settings['general']['ga_code']);
            if (empty($ga_code)) {
                return false;
            }
            
            // Do not activate valid when admin is logged in
            if (current_user_can('manage_options')) {
                return false;
            }
            
            // show google analytics javascript snippet
            include_once(TRACKINGCODESWP_VIEWS_PATH . 'google-analytics.php');
        }
        
        /**
         * Google Tag Manager code - Header
         *
         * @since 1.0.0
         */
        public function google_tag_manager_code_header() {
            // check if module is activated and code exists
            if (empty($this->plugin_settings['general']['gtm_code'])) {
                return false;
            }
            
            // sanitize code
            $gtm_code = trackingcodesforwp_sanitize_gtm_code($this->plugin_settings['general']['gtm_code']);
            if (empty($gtm_code)) {
                return false;
            }
            
            // Do not activate valid when admin is logged in
            if (current_user_can('manage_options')) {
                return false;
            }
            
            // show google analytics javascript snippet
            include_once(TRACKINGCODESWP_VIEWS_PATH . 'google-tag-manager-header.php');
        }
        
        /**
         * Google Tag Manager code
         *
         * @since 1.0.0
         */
        public function google_tag_manager_code_body() {
            // check if module is activated and code exists
            if (empty($this->plugin_settings['general']['gtm_code'])) {
                return false;
            }
            
            // sanitize code
            $gtm_code = trackingcodesforwp_sanitize_gtm_code($this->plugin_settings['general']['gtm_code']);
            if (empty($gtm_code)) {
                return false;
            }
            
            // Do not activate valid when admin is logged in
            if (current_user_can('manage_options')) {
                return false;
            }
            
            // show google analytics javascript snippet
            include_once(TRACKINGCODESWP_VIEWS_PATH . 'google-tag-manager-body.php');
        }
        
        /**
         * Google AdWords Remarketing
         *
         * @since 1.0.0
         */
        public function google_adwords_remarketing() {
            // check if module is activated and code exists
            if (empty($this->plugin_settings['general']['gar_code'])) {
                return false;
            }
            
            // sanitize code
            $gar_code = trackingcodesforwp_sanitize_gar_code($this->plugin_settings['general']['gar_code']);
            if (empty($gar_code)) {
                return false;
            }
            
            // Do not activate valid when admin is logged in
            if (current_user_can('manage_options')) {
                return false;
            }
            
            // show google analytics javascript snippet
            include_once(TRACKINGCODESWP_VIEWS_PATH . 'google-adwords-remarketing.php');
        }
        
        
        /**
         * Initialize when plugin is activated
         *
         * @since 2.0.0
         */
        public function init() {
            // Nothing to do
        }
        
        /**
         * Load languages files
         *
         * @since 1.0.0
         */
        public function load_plugin_textdomain() {
            $domain = $this->plugin_slug;
            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            
            load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
            load_plugin_textdomain($domain, FALSE, TRACKINGCODESWP_LANGUAGES_PATH);
        }
        
    }
}
    