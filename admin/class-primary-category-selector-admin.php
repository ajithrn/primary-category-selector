<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ajithrn.com
 * @since      1.0.0
 *
 * @package    Primary_Category_Selector
 * @subpackage Primary_Category_Selector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Primary_Category_Selector
 * @subpackage Primary_Category_Selector/admin
 * @author     ajith_rn <dev@ajithrn.com>
 */
class Primary_Category_Selector_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// register custom meta box to select categories.
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );

		// save custom meta data.
		add_action( 'save_post', array( $this, 'save_primary_meta_content' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Primary_Category_Selector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Primary_Category_Selector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/primary-category-selector-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Primary_Category_Selector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Primary_Category_Selector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/primary-category-selector-admin.js', array( 'jquery' ), $this->version, false );

		// Formatting categories/taxonomies for JS.
		$data = self::get_post_taxonomies();
		wp_localize_script( $this->plugin_name, 'primaryCategorySelector', $data );
	}

	/**
	 * Exclude unwanted post type which are not needed the custom metabox.
	 *
	 * @return array
	 */
	public static function get_not_included_post_types() {
		return (array) apply_filters(
			'primary_category_selector_not_included_post_types',
			array(
				'attachment' => 'attachment',
				'page'       => 'page',
			)
		);
	}

	/**
	 * Get list of post types in the website
	 *
	 * @param array  $args arguments.
	 * @param string $output  output format.
	 * @return array
	 */
	public static function get_post_types( $args = array(), $output = 'names' ) {
		$args       = apply_filters( 'primary_category_selector_post_types_args', array_merge( array( 'show_ui' => true ), $args ) );
		$post_types = array_diff_key( get_post_types( $args, $output ), self::get_not_included_post_types() );
		return (array) apply_filters( 'primary_category_selector_post_types', $post_types );
	}

	/**
	 * Exclude unwanted post type which are not needed the custom metabox
	 *
	 * @return array
	 */
	public static function get_not_included_taxonomies() {
		return (array) apply_filters(
			'primary_category_selector_not_included_taxonomies',
			array(
				'post_tag'    => 'post_tag',
				'post_format' => 'post_format',
			)
		);
	}

	/**
	 * Get list taxonomies present in the website
	 *
	 * @param array  $args arguments.
	 * @param string $output  output format.
	 * @return array
	 */
	public static function get_taxonomies( $args = array(), $output = 'objects' ) {
		$args = apply_filters(
			'primary_category_selector_taxonomies_args',
			array_merge(
				array(
					'hierarchical' => true,
					'show_ui'      => true,
				),
				$args
			)
		);
		$taxonomies = array_diff_key( get_taxonomies( $args, $output ), self::get_not_included_taxonomies() );
		return (array) apply_filters( 'primary_category_selector_taxonomies', $taxonomies );
	}

	/**
	 * Get Taxonomies enabled for current post/
	 *
	 * @param array  $args arguments.
	 * @param string $output output format.
	 * @return array
	 */
	private function get_post_taxonomies( $args = array(), $output = 'objects' ) {
		global $post;
		$taxonomies = array_diff_key(
			get_object_taxonomies(
				$post,
				'objects',
			),
			self::get_not_included_taxonomies(),
		);
		// format taxonomies that can be used for js.
		$taxonomies = array_map( array( $this, 'map_taxonomies_for_js' ), $taxonomies );
		return array( 'taxonomies' => $taxonomies );
	}

	/**
	 * Format taxonomy data for the js
	 *
	 * @param array [type] $taxonomy data.
	 * @return array
	 */
	private function map_taxonomies_for_js( $taxonomy ) {

		global $post;

		// get current primary term from meta.
		$meta_key     = 'primary-' . $taxonomy->name;
		$primary_term = get_post_meta( $post->ID, $meta_key, true );

		if ( empty( $primary_term ) ) {
			$primary_term = '';
		}

		return array(
			'title'     => $taxonomy->labels->singular_name,
			'name'      => $taxonomy->name,
			'label'     => $taxonomy->label,
			'primary'   => $primary_term,
			'gutenberg' => use_block_editor_for_post( $post ),
		);
	}

	/**
	 * Get data for meta select field
	 *
	 * @param [type] $post_id post id.
	 * @param array  $callback_args callback args.
	 * @return void
	 */
	public function get_meta_box_content( $post_id, $callback_args = array() ) {

		global $post;
		$meta_key         = 'primary-' . $callback_args['args']['taxonomy'];
		$primary_category = '';

		// Retrieve data from primary_category meta field.
		$current_selected = get_post_meta( $post->ID, $meta_key, true );

		// Set variable so that select element displays the set primary category on page load.
		if ( isset( $current_selected ) && ( '' !== $current_selected ) ) {
			$primary_category = $current_selected;
		}

		// Get list of categories/taxonomies associated with post.
		$post_categories = wp_get_post_terms( $post->ID, $callback_args['args']['taxonomy'] );
		echo '<select name="' . esc_html( $callback_args['id'] ) . '" id="' . esc_html( $callback_args['id'] ) . '" class="pcs_select" style="width:95%">';

		// Load each associated category into select element and display set primary category on page load.
		foreach ( $post_categories as $post_category ) {
			echo '<option value="' . esc_html( $post_category->term_taxonomy_id ) . '" ' . selected( $primary_category, $post_category->term_taxonomy_id, false ) . '>' . esc_html( $post_category->name ) . '</option>';
		}
		echo '</select>';
	}


	/**
	 * Register custom meta boxes to select  primary categories/taxonomies
	 *
	 * @return void
	 */
	public function register_meta_boxes() {

		$post_types = self::get_post_types();
		$taxonomies = self::get_taxonomies();

		foreach ( $taxonomies  as $taxonomy ) {
			foreach ( (array) $post_types as $post_type ) {
				if ( in_array( $post_type, $taxonomy->object_type, true ) ) {
					$meta_box_id   = 'primary-' . $taxonomy->name;
					$meta_box_name = 'Primary ' . $taxonomy->labels->singular_name;
					$callback_args = array( 'taxonomy' => $taxonomy->name );

					// add meta box.
					add_meta_box(
						$meta_box_id,
						$meta_box_name,
						array( $this, 'get_meta_box_content' ),
						$post_type,
						'side',
						'default',
						$callback_args
					);
				}
			}
		}

	}

	/**
	 * Save Selected primary category data
	 *
	 * @return void *
	 */
	public static function save_primary_meta_content() {
		global $post;
		$taxonomies = self::get_taxonomies();

		foreach ( $taxonomies  as $taxonomy ) {
			$meta_key = 'primary-' . $taxonomy->name;
			wp_verify_nonce( $meta_key );
			if ( isset( $_POST[ $meta_key ] ) ) {
				$primary_category = sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) );
				update_post_meta( $post->ID, $meta_key, $primary_category );
			}
		}
	}
}
