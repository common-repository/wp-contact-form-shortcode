<?php
/*
Plugin Name: WP Contact form Shortcode
Plugin URI: https://caodatblog.com/plugin-wp-contact-from-shortcode
Description: Custom shortcode for contact form 7
Author: Cao Dat
Version: 1.0
Author URI: http://caodatblog.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}// Exit if accessed directly

define( 'CFS_URL', plugin_dir_url( __FILE__ ) );
define( 'CFS_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, 'cfs_check_parent_plugin_activate' );
if ( ! function_exists( 'cfs_check_parent_plugin_activate' ) ) {
	function cfs_check_parent_plugin_activate() {

		// Require parent plugin
		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) and current_user_can( 'activate_plugins' ) ) {
			// Stop activation redirect and show error
			wp_die( 'Sorry, but this plugin requires the Contact Form 7 to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>' );
		}
	}
}

include_once dirname( __FILE__ ) . '/includes/CFS_ShortCode.php';

new CFS_ShortCode();
