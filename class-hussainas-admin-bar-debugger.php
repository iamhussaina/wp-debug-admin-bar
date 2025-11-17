<?php
/**
 * Core class for the Admin Bar Debugger Module.
 *
 * Handles the creation and population of the debug menu in the WordPress admin bar.
 *
 * @package   Hussainas Admin Bar Debugger
 * @version   1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hussainas_Admin_Bar_Debugger
 *
 * Encapsulates all functionality for adding a debug menu to the admin bar.
 */
class Hussainas_Admin_Bar_Debugger {

	/**
	 * A static instance of the class.
	 *
	 * @var Hussainas_Admin_Bar_Debugger|null
	 */
	private static $instance = null;

	/**
	 * Initializes the class.
	 *
	 * Ensures only one instance of the class is loaded (Singleton pattern)
	 * and hooks into WordPress.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 * Sets up the main action hook.
	 */
	private function __construct() {
        // Hook into the admin bar menu generation.
        // Use a high priority (999) to add the menu towards the end.
		add_action( 'admin_bar_menu', [ $this, 'hussainas_add_debug_menu' ], 999 );
	}

	/**
	 * Adds the main debug menu and its sub-items to the admin bar.
	 *
	 * This is the primary callback function that builds the menu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
	 */
	public function hussainas_add_debug_menu( $wp_admin_bar ) {
		// 1. Security Check: Only show for administrators.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// 2. Add Parent Node: The main "Debug" menu item.
		$wp_admin_bar->add_node( [
			'id'    => 'hussainas_debug_bar',
			'title' => 'Hussainas Debug',
			'href'  => '#',
		] );

		// 3. Add Template File Node.
		$this->hussainas_add_template_node( $wp_admin_bar );

		// 4. Add Conditional Tags Nodes.
		$this->hussainas_add_conditionals_node( $wp_admin_bar );

		// 5. Add Main Query Vars Nodes.
		$this->hussainas_add_query_vars_node( $wp_admin_bar );
	}

	/**
	 * Adds the currently loaded template file information.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
	 */
	private function hussainas_add_template_node( $wp_admin_bar ) {
		global $template;
		$template_file = $template ? basename( $template ) : 'N/A';

		$wp_admin_bar->add_node( [
			'id'     => 'hussainas_debug_template',
			'title'  => 'Template: ' . $template_file,
			'parent' => 'hussainas_debug_bar',
			'href'   => false,
		] );
	}

	/**
	 * Adds a sub-menu displaying all TRUE conditional tags.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
	 */
	private function hussainas_add_conditionals_node( $wp_admin_bar ) {
		// Parent node for conditionals
		$wp_admin_bar->add_node( [
			'id'     => 'hussainas_debug_conditionals',
			'title'  => 'Conditionals (True)',
			'parent' => 'hussainas_debug_bar',
			'href'   => false,
		] );

		// List of common conditionals to check.
		$conditionals = [
			'is_front_page',
			'is_home',
			'is_singular',
			'is_single',
			'is_page',
			'is_attachment',
			'is_archive',
			'is_category',
			'is_tag',
			'is_tax',
			'is_author',
			'is_date',
			'is_year',
			'is_month',
			'is_day',
			'is_time',
			'is_search',
			'is_404',
			'is_paged',
		];

		$true_conditionals = 0;

		foreach ( $conditionals as $conditional ) {
			// Check if the function exists and evaluates to true.
			if ( function_exists( $conditional ) && call_user_func( $conditional ) ) {
				$wp_admin_bar->add_node( [
					'id'     => 'hussainas_cond_' . $conditional,
					'title'  => $conditional . '()',
					'parent' => 'hussainas_debug_conditionals',
					'href'   => false,
				] );
				$true_conditionals++;
			}
		}

		// If no conditionals were true, add a placeholder.
		if ( 0 === $true_conditionals ) {
			$wp_admin_bar->add_node( [
				'id'     => 'hussainas_cond_none',
				'title'  => '(None True)',
				'parent' => 'hussainas_debug_conditionals',
				'href'   => false,
			] );
		}
	}

	/**
	 * Adds a sub-menu displaying the main WP_Query's query variables.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
	 */
	private function hussainas_add_query_vars_node( $wp_admin_bar ) {
		global $wp_query;

		// Parent node for query vars
		$wp_admin_bar->add_node( [
			'id'     => 'hussainas_debug_query',
			'title'  => 'Main Query Vars',
			'parent' => 'hussainas_debug_bar',
			'href'   => false,
		] );

		// Filter out empty query vars for a cleaner display.
		$query_vars = array_filter( $wp_query->query_vars );

		if ( empty( $query_vars ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'hussainas_query_none',
				'title'  => '(No query vars set)',
				'parent' => 'hussainas_debug_query',
				'href'   => false,
			] );
			return;
		}

		// Add each query var as a sub-item.
		foreach ( $query_vars as $key => $value ) {
			// Convert arrays/objects to string for display.
			if ( is_array( $value ) || is_object( $value ) ) {
				$value = wp_json_encode( $value );
			}

			$wp_admin_bar->add_node( [
				'id'     => 'hussainas_query_' . esc_attr( $key ),
				'title'  => esc_html( $key . ': ' . $value ),
				'parent' => 'hussainas_debug_query',
				'href'   => false,
			] );
		}
	}
}
