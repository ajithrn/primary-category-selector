<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://ajithrn.com
 * @since      1.0.0
 *
 * @package    Primary_Category_Selector
 * @subpackage Primary_Category_Selector/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Primary_Category_Selector
 * @subpackage Primary_Category_Selector/includes
 * @author     ajith_rn <dev@ajithrn.com>
 */
class Primary_Category_Selector_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'primary-category-selector',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
