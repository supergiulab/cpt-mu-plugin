<?php
/**
 * Register Custom Post Types for this Wordpress Site
 * 
 * Version: 1.0.0
 * Author: SuPerGiu
 * Author URI: https://supergiulab.com
 * Text Domain: site_theme_cpt
 * Domain Path: /site-custom-post-types/languages/
 * 
 * @package $theme_or_site_name
 */
defined( 'ABSPATH' ) or die( 'No no no' );

class SiteCustomPostTypes
{
	private $cpts;

	public function __construct() {
		add_action( 'init', array($this, 'init') );
		add_action( 'muplugins_loaded', array($this, 'plugins_loaded') );
		add_filter( 'pre_get_posts', array($this, 'pre_get_posts') );
	}

	/**
	 * Init Plugin
	 * 
	 * @uses init action
	 */
	public function init() {
		$this->define_post_types();
		$this->register_post_type();
		flush_rewrite_rules();
	}

	/**
	 * Plugin is Loaded
	 * 
	 * @uses muplugins_loaded action
	 */
	public function plugins_loaded() {
		$this->register_text_domain();
	}

	/**
	 * Get Posts
	 * 
	 * @uses pre_get_posts filter
	 */
	public function pre_get_posts() {
		$this->archive_posts_per_page();
	}

	/**
	 * Define Custom Post Types and their Settings
	 * 
	 * @see register_post_type() | $labels | $rewrite | $args
	 */
	private function define_post_types() {
		$this->cpts = array(
			'products' => array(
				'singular_name' => __('Product', 'site_theme_cpt'),
				'plural_name' => __('Products', 'site_theme_cpt'),
				'item' => __('Item', 'site_theme_cpt'),
				'items' => __('Items', 'site_theme_cpt'),
				'supports' => array( 'title', 'editor' ),
				'archive_posts' => 1,
				'icon' => 'dashicons-archive'
			),
			'reviews' => array(
				'slug' => __('reviews', 'site_theme_cpt'),
				'singular_name' => __('Review', 'site_theme_cpt'),
				'plural_name' => __('Reviews', 'site_theme_cpt'),
				'item' => __('Thing', 'site_theme_cpt'),
				'items' => __('Things', 'site_theme_cpt'),
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'archive_posts' => 3,
				'icon' => 'dashicons-format-aside'
			)
		);
	}

	/**
	 * Loop Custom Post Types and Register
	 * 
	 * @uses register_post_type()
	 */
	private function register_post_type() {
		if ( ! isset( $this->cpts ) || empty(( $this->cpts )) ) return;

		foreach ($this->cpts as $cpt => $params) :

			$singular = $params["singular_name"];
			$plural = $params["plural_name"];
			$item = $params["item"];
			$items = $params["items"];
			$supports = $params["supports"];
			$archive_posts = $params["archive_posts"];
			$icon = $params["icon"];

			$labels = array(
				'name'                => sprintf( _x( '%s', 'Post Type General Name', 'site_theme_cpt' ), $plural ),
				'singular_name'       => sprintf( _x( '%s', 'Post Type Singular Name', 'site_theme_cpt' ), $singular ),
				'menu_name'           => sprintf( __( '%s', 'site_theme_cpt' ), $plural ),
				'name_admin_bar'      => sprintf( __( '%s', 'site_theme_cpt' ), $plural ),
				'parent_item_colon'   => __( 'Parent element:', 'site_theme_cpt' ),
				'all_items'           => sprintf( __( 'All %s', 'site_theme_cpt' ), $plural ),
				'add_new_item'        => sprintf( __( 'Add New %s', 'site_theme_cpt' ), $item ),
				'add_new'             => sprintf( __( 'Add %s', 'site_theme_cpt' ), $item ),
				'new_item'            => sprintf( __( 'New %s', 'site_theme_cpt' ), $item ),
				'edit_item'           => sprintf( __( 'Modify %s', 'site_theme_cpt' ), $item ),
				'update_item'         => sprintf( __( 'Update %s', 'site_theme_cpt' ), $item ),
				'view_item'           => sprintf( __( 'View %s', 'site_theme_cpt' ), $items ),
				'search_items'        => sprintf( __( 'Search %s', 'site_theme_cpt' ), $items ),
				'not_found'           => __( 'Not found', 'site_theme_cpt' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'site_theme_cpt' ),
			);
			$rewrite = array(
				'slug'                => $cpt,
				'with_front'          => true,
				'pages'               => true,
				'feeds'               => true,
			);
			$args = array(
				'label'               => sprintf( __( '%s', 'site_theme_cpt' ), $cpt ),
				'description'         => __( 'Custom Post Type per Site Name', 'site_theme_cpt' ),
				'labels'              => $labels,
				'supports'            => $supports,
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'           => (version_compare($GLOBALS['wp_version'], '3.8', '>=')) ? $icon : false,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'rewrite'             => $rewrite,
				'capability_type'     => 'post',
			);
			register_post_type( $cpt, $args );

		endforeach;
	}

	/**
	 * Register Text Domain
	 * 
	 * @uses load_muplugin_textdomain()
	 */
	private function register_text_domain() {
		load_muplugin_textdomain( 'site_theme_cpt', '/site-custom-post-types/languages/' );
	}

	/**
	 * Set Posts per Page in Custom Post Types Archive
	 * 
	 * @uses is_post_type_archive() | $wp_query | pre_get_posts filter
	 */
	private function archive_posts_per_page() {
		global $wp_query;

		if ( is_admin() || ! $wp_query->is_main_query() ) return;

		foreach ($this->cpts as $cpt => $params) :

			if ( is_post_type_archive( $cpt ) ) {
				$wp_query->set( 'posts_per_page', $params['archive_posts'] );
			}

		endforeach;
	}

}

return new SiteCustomPostTypes();