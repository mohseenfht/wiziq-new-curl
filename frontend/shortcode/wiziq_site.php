<?php 
/*
 * Add shortcode for frontend.
 */ 

add_shortcode('WizIQ','wiziq_shortcode');

function wiziq_shortcode(){

include 'shortcode/shortcode_wiziq.php';

}

/*
 * Add css in front end 
 */
add_action('wp_head','add_css_wiziqfront');

function add_css_wiziqfront(){
 wp_register_style( 'frontendstylesheet', WIZIQ_PLUGINURL_PATH.'stylesheet/frontend.css' );
 wp_enqueue_style( 'frontendstylesheet' );
 wp_register_style('jquery-ui-customsite-css', WIZIQ_PLUGINURL_PATH . 'stylesheet/jquery-ui-1.10.3.css');
 wp_enqueue_style( 'jquery-ui-customsite-css' ); 
 wp_enqueue_script('jquery');
 wp_enqueue_script('jquery-ui-datepicker');
 wp_enqueue_script( 'wiziq_front_js', WIZIQ_PLUGINURL_PATH . 'js/wiziq_front_custom.js'); 

}

