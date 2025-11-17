<?php
/**
 * Loader for the Admin Bar Debugger Module.
 *
 * This file includes the main class and initializes the debug functionality.
 *
 * @package   Hussainas Admin Bar Debugger
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     MIT
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define a constant for the module path for easier inclusion.
define( 'HUSSAINAS_DEBUG_MODULE_PATH', trailingslashit( __DIR__ ) );

// Include the main class file.
require_once HUSSAINAS_DEBUG_MODULE_PATH . 'class-hussainas-admin-bar-debugger.php';

/**
 * Initializes the debugger module.
 *
 * This function hooks into 'after_setup_theme' to ensure all
 * WordPress functions are available and then instantiates the main class.
 */
function hussainas_run_admin_bar_debugger() {
    // Check if the class exists before trying to use it.
	if ( class_exists( 'Hussainas_Admin_Bar_Debugger' ) ) {
		Hussainas_Admin_Bar_Debugger::init();
	}
}
// Use 'init' hook to ensure user is authenticated and WP object is available.
add_action( 'init', 'hussainas_run_admin_bar_debugger' );
