<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Trackingcodes_for_WP_Admin')) {
    
    class Trackingcodes_for_WP_Admin {
        
        protected static $instance = null;
        protected $plugin_slug;
        protected $plugin_settings;
        protected $plugin_default_settings;
        protected $plugin_basename;
        protected $plugin_screen_hook_suffix = null;
        private $dismissed_notices_key = 'trackingcodesforwp_dismissed_notices';
        
        private function __construct() {
            $plugin = Trackingcodes_for_WP::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();
            $this->plugin_settings = $plugin->get_plugin_settings();
            $this->plugin_default_settings = $plugin->default_settings();
            $this->plugin_basename = plugin_basename(TRACKINGCODESWP_PATH . $this->plugin_slug . '.php');
            
            // Load admin style sheet and JavaScript.
            
            /* I do not use this for now */
            // add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
            // add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            
            // Add the options page and menu item.
            add_action('admin_menu', array($this, 'add_plugin_menu'));
        }
        
        /**
         * Add plugin in Settings menu
         *
         * @since 1.0.0
         */
        public function add_plugin_menu() {
            $name = TRACKINGCODESWP_PLUGIN_NAME;
            
            $this->plugin_screen_hook_suffix = add_options_page(
                __($name . ' Options', $this->plugin_slug),
                __($name, $this->plugin_slug),
                'manage_options',
                $this->plugin_slug,
                array($this, 'display_plugin_settings')
                );
        }
        
        
        public static function get_instance() {
            if (null == self::$instance) {
                self::$instance = new self;
            }
            
            return self::$instance;
        }
        
        /**
         * Delete cache if any cache plugin (wp_cache or w3tc) is activated
         *
         * @since 1.0.0
         */
        public function delete_cache() {
            // Super Cache Plugin
            if (function_exists('wp_cache_clear_cache')) {
                wp_cache_clear_cache(is_multisite() && is_plugin_active_for_network($this->plugin_basename) ? get_current_blog_id() : '');
            }
            
            // W3 Total Cache Plugin
            if (function_exists('w3tc_pgcache_flush')) {
                w3tc_pgcache_flush();
            }
        }
        
        /**
         * Settings page
         *
         * @since 1.0.0
         * @global object $wp_roles
         */
        public function display_plugin_settings() {
            global $wp_roles;
            
            // save settings
            $this->save_plugin_settings();
            
            // show settings
            include_once(TRACKINGCODESWP_VIEWS_PATH . 'settings.php');
        }
        
        /**
         * Load CSS files
         *
         * @since 1.0.0
         * @return type
         * @notes I do not use this for now
         */
        /*
        public function enqueue_admin_styles() {
            wp_enqueue_style($this->plugin_slug . '-admin-styles', TRACKINGCODESWP_CSS_URL . 'style-admin' . TRACKINGCODESWP_ASSETS_SUFFIX . '.css', array(), Trackingcodes_for_WP::VERSION);
        }
        */
        
        /**
         * Load JS files and their dependencies
         *
         * @since 1.0.0
         * @return
         * @notes I do not use this for now
         */
        /*
        public function enqueue_admin_scripts() {
            wp_enqueue_script($this->plugin_slug . '-admin-script', TRACKINGCODESWP_JS_URL . 'script-admin' . TRACKINGCODESWP_ASSETS_SUFFIX . '.js', array(), Trackingcodes_for_WP::VERSION);
        }
        */
        
        /**
         * Save settings
         *
         * @since 1.0.0
         */
        public function save_plugin_settings() {
            // Check for POST
            if (!empty($_POST) && !empty($_POST['tab'])) {
                // Check nonce
                if (!wp_verify_nonce($_POST['_wpnonce'], 'tab-' . $_POST['tab'])) {
                    die(__('Security check.', $this->plugin_slug));
                }
                
                // DO SOME SANITIZATIONS
                $tab = $_POST['tab'];
                switch ($tab) {
                    case 'general':
                        // GOOGLE ANALYTICS
                        $_POST['options']['general']['ga_code'] = trackingcodesforwp_sanitize_ga_code($_POST['options']['general']['ga_code']);
                        // GOOGLE TAG MANAGER
                        $_POST['options']['general']['gtm_code'] = trackingcodesforwp_sanitize_gtm_code($_POST['options']['general']['gtm_code']);
                        // GOOGLE ADWORDS REMARKETING
                        $_POST['options']['general']['gar_code'] = trackingcodesforwp_sanitize_gar_code($_POST['options']['general']['gar_code']);
                        break;
                }
                
                $this->plugin_settings[$tab] = $_POST['options'][$tab];
                update_option('trackingcodesforwp_settings', $this->plugin_settings);
            }
        }
        
        /**
         * Register all plugin settings at admin
         *
         * @since 1.0.0
         */
        public function settings_api_init()
        {
            // Add the section to the Tools settings
            add_settings_section(
                $this->plugin_slug . 'settings_section'
                ,		__(TRACKINGCODESWP_PLUGIN_NAME, $this->plugin_slug)
                ,		array( $this, 'settings_section')
                ,	  'writting'
                );
        }
        
    }
}
