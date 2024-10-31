<?php
class RGPGrid
{
	function __construct() {
		add_action('admin_menu', array(&$this,'rgpg_plugin_admin_menu'));
		add_action('admin_init', array(&$this,'rgpg_backend_scripts'));
		add_action('init', array(&$this,'rgpg_portfolio_post_type'));
		add_action('init', array(&$this,'rgpg_portfolio_category'));
		add_action('init', array(&$this,'rgpg_portfolio_default_category'));
		add_action('wp_enqueue_scripts', array(&$this,'rgpg_frontend_scripts'));
		add_action('save_post', array(&$this,'rgpg_default_object_terms'), 100, 2);
		add_action( 'add_meta_boxes',  array(&$this,'add_rgpg_metaboxes') );
    }
	
	
	function rgpg_plugin_admin_menu() {
		add_submenu_page('edit.php?post_type=rgpg_portfolio', 'Create Shortcode', 'RGPG Shortcode', 'edit_pages','rgpg_createsc','rgpg_createsc_func');
		add_submenu_page('edit.php?post_type=rgpg_portfolio', 'Edit RGPG Shortcode', '', 'edit_pages','rgpg_editsc','rgpg_editsc_func');
	}
	
	function rgpg_backend_scripts() {
		if(is_admin()){
			wp_enqueue_style('rgpg_backend_style',plugins_url('css/rgpg-admin.css',__FILE__), false, '1.0.0' );
			wp_enqueue_script('rgpg_backend_script',plugins_url('js/rgpg-admin.js',__FILE__), array('jquery','wp-color-picker'));	
		}
	}
	
	function rgpg_frontend_scripts() {	
		if(!is_admin()){
			wp_enqueue_script('jquery');
			wp_enqueue_script('rgpgrid-modernizr',plugins_url('js/rgpg-modernizr.custom.js',__FILE__), array('jquery'),'',2);
			wp_enqueue_script('rgpgrid',plugins_url('js/rgpg-grid.js',__FILE__), array('jquery'),'',2);
			wp_enqueue_style('rgpgrid',plugins_url('css/rgpgrid.css',__FILE__));
		}
	}
	
	function rgpg_portfolio_post_type() 
	{
		register_post_type( 'rgpg_portfolio',
			array(
				'labels' => array(
				'name' => __('RGPG Portfolio', 'rgpgrid' ),
				'singular_name' => __( 'portfolio', 'rgpgrid' ),
				'add_new' => __('Add Portfolio', 'rgpgrid'),
				'add_new_item' => __('Add New Portfolio', 'rgpgrid'),
				'edit_item' => __('Edit Portfolio', 'rgpgrid'),
				'new_item' => __('New Portfolio', 'rgpgrid'),
				'all_items' => __('All Portfolio', 'rgpgrid'),
				'view_item' => __('View Portfolio', 'rgpgrid')
				),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_icon' =>  'dashicons-portfolio',
		'rewrite' => array('slug' => 'rgpg-portfolio'),
		'supports' => array('title','thumbnail','editor'),
		'taxonomies'=>array('rgpg_portfolio_category','rgpg_portfolio_tag')
		));
	}
	
	function rgpg_portfolio_category() 
	{
		// CREATE A NEW TAXONOMY
		register_taxonomy(
			'rgpg_category',
			'rgpg_portfolio',
			array(
				'hierarchical' => true,
				'label' => __( 'Portfolio Categories', 'rgpgrid' ),
				'singular_label' => __( 'Portfolio Category', 'rgpgrid' ),
				'rewrite' => array( 'slug' => 'rgpg_category')
			)
		);
	}
	
	function rgpg_portfolio_default_category()
	{
		$parent_term = term_exists( 'uncategorized', 'rgpg_category' ); // ARRAY IS RETURNED IF TAXONOMY IS GIVEN
		$parent_term_id = $parent_term['term_id']; // GET NUMERIC TERM ID
		if ($parent_term == 0 && $parent_term == null) {
			wp_insert_term(
				'uncategorized', // THE TERM 
				'rgpg_category',  // THE TAXONOMY
				array(
					'description'=> 'Uncategorized',
					'slug' => 'uncategorized',
					'parent'=> $parent_term_id
				)
			);
		}
	}
	
	function rgpg_default_object_terms( $post_id, $post ) 
	{
		if ( 'publish' === $post->post_status ) {
			$defaults = array(
				'rgpg_category' => array( 'uncategorized' ),
			);

			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( (array) $taxonomies as $taxonomy ) {
				$terms = wp_get_post_terms( $post_id, $taxonomy );
				if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
					wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
				}
			}
		}
		
		//we're authenticated: we need to find and save the data
		$status = isset($_POST['_rgpg_portfolio_link'])?$_POST['_rgpg_portfolio_link']:'';
		// save data in INVISIBLE custom field (note the "_" prefixing the custom fields' name
		update_post_meta($post_id, '_rgpg_portfolio_link', $status);
	}
	
	// Add the Portfolio Meta Boxes
	function add_rgpg_metaboxes() {
		add_meta_box('rgpg_page', 'Please enter a custom link for this portfolio',  array(&$this,'rgpg_portfolio_link'), 'rgpg_portfolio');
	}
	// The Portfolio Location Metabox

	function rgpg_portfolio_link() {	
		global $post;
		
		$custom = get_post_custom($post->ID);
		$_rgpg_portfolio_link = (isset($custom["_rgpg_portfolio_link"][0])) ? $custom["_rgpg_portfolio_link"][0] : '';
		
		echo '<input type="text" name="_rgpg_portfolio_link" style="height:33px !important" value="' . $_rgpg_portfolio_link  . '" class="widefat" />';

	}

	function rgpg_limit_words($string, $word_limit) {
		$words = explode(' ', $string);
		return implode(' ', array_slice($words, 0, $word_limit));
	}
}
?>