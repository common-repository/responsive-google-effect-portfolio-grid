<?php
/*
Plugin Name: Responsive Google Effect Portfolio Grid
Plugin URI: http://www.wptreasure.com
Description: Responsive Google Effect Portfolio Grid is a awesome plugin used to create your own portfolio grid with a google effect.
Author: WPTreasure
Version: 1.0.2
Author URI: http://www.wptreasure.com
*/
// ----------------------------------------------
// DECLARE REQUIRED CONSTANTS
define('RGPGRID_URL', plugin_dir_url(__FILE__));
define('RGPGRID_PATH', plugin_dir_path(__FILE__));

// INCLUDE REQUIRED FILES
include_once('rgpg-class.php');
include_once('rgpg-display-slider.php');
include_once('rgpg-shortcode-info.php');
// ----------------------------------------------

$rgpgObj = new rgpgrid(); 

function rgpg_get_version(){
  if (!function_exists( 'get_plugins' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

// Add shortcode
function rgpg_short_code($atts) {
	ob_start();
    extract(shortcode_atts(array(
		"name" => ''
	), $atts));
	RGPGrid($atts);
	$output = ob_get_clean();
	return $output;
}
add_shortcode('RGPGrid', 'rgpg_short_code');
?>