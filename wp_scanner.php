<?php
/*
Plugin Name: WP Scanner
Plugin URI:
Description:
Version: 0.1
Author: Greg Molnar
Author URI: greg.molnar.io
License: GPL2
*/
/*
Copyright 2015  Greg Molnar

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!class_exists('WPScanner'))
{
    class WPScanner
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
          add_action('admin_init', array(&$this, 'admin_init'));
          add_action('admin_menu', array(&$this, 'add_menu'));
        }

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
            // Set up the settings for this plugin
            $this->init_settings();
            // Possibly do additional admin_init tasks
        }

        /**
         * Initialize some custom settings
         */
        public function init_settings()
        {
            // register the settings for this plugin
            register_setting('wp_scanner-group', 'api_key');
        }

        /**
         * add a menu
         */
        public function add_menu()
        {
            add_options_page('WP Scanner Settings', 'WP Scanner', 'manage_options', 'wp_scanner', array(&$this, 'plugin_settings_page'));
        }

        /**
         * Menu Callback
         */
        public function plugin_settings_page()
        {
            if(!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
            include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
          // Do nothing
        }
    }
}

if(class_exists('WPScanner'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WPScanner', 'activate'));
    register_deactivation_hook(__FILE__, array('WPScanner', 'deactivate'));

    // instantiate the plugin class
    $wp_scanner = new WPScanner();
    // Add a link to the settings page onto the plugin page
    if(isset($wp_scanner))
    {
        // Add the settings link to the plugins page
        function plugin_settings_link($links)
        {
            $settings_link = '<a href="options-general.php?page=wp_scanner">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        function api_request_handler() {
           if($_SERVER["REQUEST_URI"] == '/wp_scanner/api') {
              $headers = getallheaders();
              $api_key = get_option('api_key');
              if(isset($headers['AUTH_TOKEN']) && $headers['AUTH_TOKEN'] == $api_key){
                $response = array();
                $response['plugins'] = array();
                foreach (get_option('active_plugins') as $plugin) {
                  $response['plugins'][] = array_merge(get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin));
                }
                $theme = wp_get_theme();
                $response['theme']['name'] = $theme["Name"];
                $response['theme']['version'] = $theme["Version"];
                $response['info']['version'] = get_bloginfo('version');
                if(file_exists(ABSPATH.'/readme.html'))
                  $response['info']['readme'] = true;
                echo json_encode($response);
              }
              exit();
           }
        }
        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", 'plugin_settings_link');
        add_action('parse_request', 'api_request_handler');
    }
}
