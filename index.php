<?php
/*
 * Plugin Name: CPM WP All in one responsive Poll.
 * Plugin URI:  https://codepixelz.market
 * Description: Your ultimate poll solution. CPM WP All in One Poll lets you create 7 different Poll types, is fully translatable. Has Widgets and Shortcode. Supports multiple polls in a single page and you can also simply show the results of the poll.
 * Version:     1.0
 * Author:      codepixelzmedia
 * Author URI:  http://codepixelz.market
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _cpmpoll
 */

/**
 * Check technical requirements are fulfilled before activating.
 **/
function cpm_wp_poll_activate(){
	if (  version_compare( PHP_VERSION, '5.1.2', '<' ) ) {
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
		wp_die( sprintf( __( "Sorry, but you can not run CPM Poll. It requires PHP 5.1.2 or newer. Please contact your web host and request they <a href='http://www.php.net/manual/en/migration5.php'>migrate</a> your PHP installation to run CPM Poll.<br/><a href=\"%s\">Return to Plugins Admin page &raquo;</a>", '_cpmpoll'), admin_url( 'plugins.php' ) ), '_cpmpoll' );
	}
}
register_activation_hook( __FILE__, 'cpm_wp_poll_activate' );

require_once( dirname( __FILE__ ) . '/constants.php' );
require_once( dirname( __FILE__ ) . '/media.php' );
require_once( dirname( __FILE__ ) . '/admin.php' );
require_once( dirname( __FILE__ ) . '/includes/frontend.php');
require_once( dirname( __FILE__ ) . '/includes/backend.php');
require_once( dirname( __FILE__ ) . '/includes/misc_functions.php');
require_once( dirname( __FILE__ ) . '/includes/chart_makers.php');
