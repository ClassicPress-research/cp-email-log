<?php
/*
 * Plugin Name: SC Email Log
 * Plugin URI: https://github.com/simplycomputing/sc-email-log
 * Description: Logs every email sent through ClassicPress
 *
 * Version: 1.0.1
 *
 * Author: Alan Coggins 
 * Author URI: https://simplycomputing.com.au
 * License: GPLv2
 * Text Domain: email-log
 * Domain Path: languages/
 */

/*
 * Copyright 2019  Alan Coggins  (email : mail@simplycomputing.com.au)
 * Based on a plugin by Sudar Muthu.
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Load the Update Client. Yep, that's it.
require_once('include/UpdateClient.class.php');

/**
 * Load Email Log plugin.
 *
 * @since 2.0
 *
 * @param string $plugin_file Main plugin file.
 */

function load_email_log( $plugin_file ) {
	global $email_log;

	$plugin_dir = plugin_dir_path( $plugin_file );

	// setup autoloader.
	require_once 'include/EmailLogAutoloader.php';

	$loader = new \EmailLog\EmailLogAutoloader();
	$loader->add_namespace( 'EmailLog', $plugin_dir . 'include' );
	$loader->add_namespace( 'Sudar\\WPSystemInfo', $plugin_dir . 'vendor/sudar/wp-system-info/src/' );

	if ( file_exists( $plugin_dir . 'tests/' ) ) {
		// if tests are present, then add them.
		$loader->add_namespace( 'EmailLog', $plugin_dir . 'tests/wp-tests' );
	}

	$loader->add_file( $plugin_dir . 'include/Util/helper.php' );

	$loader->register();

	$email_log = new \EmailLog\Core\EmailLog( $plugin_file, $loader, new \EmailLog\Core\DB\TableManager() );

	$email_log->add_loadie( new \EmailLog\Core\EmailLogger() );
	$email_log->add_loadie( new \EmailLog\Core\UI\UILoader() );

	$email_log->add_loadie( new \EmailLog\Core\Request\NonceChecker() );
	$email_log->add_loadie( new \EmailLog\Core\Request\LogListAction() );

	$capability_giver = new \EmailLog\Core\AdminCapabilityGiver();
	$email_log->add_loadie( $capability_giver );

	// `register_activation_hook` can't be called from inside any hook.
	register_activation_hook( $plugin_file, array( $email_log->table_manager, 'on_activate' ) );
	register_activation_hook( $plugin_file, array( $capability_giver, 'add_cap_to_admin' ) );

	// Ideally the plugin should be loaded in a later event like `init` or `wp_loaded`.
	// But some plugins like EDD are sending emails in `init` event itself,
	// which won't be logged if the plugin is loaded in `wp_loaded` or `init`.
	add_action( 'plugins_loaded', array( $email_log, 'load' ), 101 );
}

/**
 * Return the global instance of Email Log plugin.
 * Eventually the EmailLog class might become singleton.
 *
 * @since 2.0
 *
 * @global \EmailLog\Core\EmailLog $email_log
 *
 * @return \EmailLog\Core\EmailLog
 */
function email_log() {
	global $email_log;

	return $email_log;
}

load_email_log( __FILE__ );

