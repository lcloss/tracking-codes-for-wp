<?php
/*
 Plugin Name: Tracking Codes for WP
 Plugin URI: https://github.com/lcloss/tracking-codes-for-wp.git
 Description: Add Tracking Codes in your WordPress site easly
 Version: 1.0.0
 Author: Luciano Closs
 Author URI: https://lucianocloss.com
 License: GPLv3
 Text Domain: trackingcodes-for-wp
 Domain Path: /languages
 */

/*
 *  Copyright (C) 2017 Luciano Closs <info@lucianocloss.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

/**
 * Change log
 * 
 * 2017-12-08 Luciano Closs - Initial version
 */
 
/**
 * Exit if access directly
 */
if ( ! defined ( 'ABSPATH' ) ) {
    die('You are not allowed to do this. Be aware.');
}

if ( defined ('TRACKINGCODESWP_PLUGIN_NAME')) {
    function trackingcodesforwp_admin_notices() {
      global $tcmp; ?>
      <div style="clear:both"></div>
      <div class="error iwp" style="padding:10px;">
          <?php $tcmp->Lang->P('PluginAlreadyInstalled'); ?>
      </div>
      <div style="clear:both"></div>
  <?php }
  add_action('admin_notices', 'trackingcodesforwp_admin_notices');
  return;

}

/**
 * PLUGIN SETTINGS
 */
define('TRACKINGCODESWP_PLUGIN_NAME', 'Tracking Codes for WP');
define('TRACKINGCODESWP_PLUGIN_SLUG', 'trackingcodes-for-wp');
define('TRACKINGCODESWP_PLUGIN_VERSION', '1.0.0');
define('TRACKINGCODESWP_PLUGIN_AUTHOR', 'Luciano Closs');

/**
 * DEFINE PATHS
 */
define('TRACKINGCODESWP_PATH', plugin_dir_path(__FILE__));
define('TRACKINGCODESWP_CLASSES_PATH', TRACKINGCODESWP_PATH . 'includes/classes/');
define('TRACKINGCODESWP_FUNCTIONS_PATH', TRACKINGCODESWP_PATH . 'includes/functions/');
define('TRACKINGCODESWP_LANGUAGES_PATH', TRACKINGCODESWP_PATH . 'languages/');
define('TRACKINGCODESWP_VIEWS_PATH', TRACKINGCODESWP_PATH . 'views/');

/* I do not use this for now */
// define('TRACKINGCODESWP_JS_PATH', TRACKINGCODESWP_PATH . 'assets/js/');
// define('TRACKINGCODESWP_CSS_PATH', TRACKINGCODESWP_PATH . 'assets/css/');

/**
 * DEFINE URLS
 */
define('TRACKINGCODESWP_URL', plugin_dir_url(__FILE__));

/* I do not use this for now */
// define('TRACKINGCODESWP_JS_URL', TRACKINGCODESWP_URL . 'assets/js/');
// define('TRACKINGCODESWP_CSS_URL', TRACKINGCODESWP_URL . 'assets/css/');

/**
 * OTHER DEFINES
 */
define('TRACKINGCODESWP_CLASS_NAME', 'Trackingcodes_for_WP');
define('TRACKINGCODESWP_ASSETS_SUFFIX', (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min');

/**
 * FUNCTIONS
 */
require_once(TRACKINGCODESWP_FUNCTIONS_PATH . 'helpers.php');

/**
 * FRONTEND
 */
$plugin_class = TRACKINGCODESWP_CLASS_NAME;
$plugin_admin_class = $plugin_class . '_Admin';

require_once(TRACKINGCODESWP_CLASSES_PATH . TRACKINGCODESWP_PLUGIN_SLUG . '-shortcodes.php');
require_once(TRACKINGCODESWP_CLASSES_PATH . TRACKINGCODESWP_PLUGIN_SLUG . '.php');
register_activation_hook(__FILE__, array($plugin_class, 'activate'));
register_deactivation_hook(__FILE__, array($plugin_class, 'deactivate'));

add_action('plugins_loaded', array($plugin_class, 'get_instance'));

/**
 * DASHBOARD
 */
if (is_admin()) {
    require_once(TRACKINGCODESWP_CLASSES_PATH . TRACKINGCODESWP_PLUGIN_SLUG . '-admin.php');
    add_action('plugins_loaded', array($plugin_admin_class, 'get_instance'));
}
