<?php
/**
* Plugin Name: Make Cart to Quote for Woocommerce
* Description: customer can be quote of cart in woocommerce products
* Version: 1.0
* Copyright: 2023
* Text Domain: make-cart-to-quote-for-woocommerce
* Domain Path: /languages 
*/
if (!defined('MCTQFW_PLUGIN_URL')) {
  define('MCTQFW_PLUGIN_URL',plugins_url('', __FILE__));
}
if (!defined('MCTQFW_PLUGIN_DIR')) {
    define('MCTQFW_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// define for base name
define('MCTQFW_BASE_NAME', plugin_basename(__FILE__));

// define for plugin file
define('MCTQFW_plugin_file', __FILE__);


include_once(MCTQFW_PLUGIN_DIR.'includes/frontend.php');
include_once(MCTQFW_PLUGIN_DIR.'includes/admin.php');

add_action( 'wp_enqueue_scripts',   'MCTQFW_load_script_style');
function MCTQFW_load_script_style() {
	wp_enqueue_style( 'MCTQFW-front-css',MCTQFW_PLUGIN_URL . '/public/css/design.css', false, '1.0.0' );
	wp_enqueue_script('jquery', false, array(), false, false);
	wp_enqueue_script( 'woocommerce-ajax-add-to-cart', MCTQFW_PLUGIN_URL . '/public/js/design.js', false, '1.0.0' );
	$passarray =  array( 
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'mctqfw_bar_width' => get_option("bar_width",'400'),
  );
  wp_localize_script( 'woocommerce-ajax-add-to-cart', 'mctqfwproductid', $passarray);
}

function MCTQFW_load_admin_script(){
  if ( isset($_GET['page']) && $_GET['page'] === 'mctqfw_cart_to_quote_generator') {
  	wp_enqueue_script('jquery', false, array(), false, false);

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-alpha', MCTQFW_PLUGIN_URL . '/admin/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '3.0.2', true );
    wp_add_inline_script(
      'wp-color-picker-alpha',
      'jQuery( function() { jQuery( ".color-picker" ).wpColorPicker(); } );'
    );

    wp_enqueue_style( 'jquery-admin-style', MCTQFW_PLUGIN_URL. '/admin/css/design.css', '', '1.0.0' );
    wp_enqueue_script( 'jquery-admin-quote', MCTQFW_PLUGIN_URL. '/admin/js/design.js', array('jquery'), '1.0');
  }

}
add_action( 'admin_enqueue_scripts', 'MCTQFW_load_admin_script' );
