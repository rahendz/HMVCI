<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! function_exists ( 'get_header' ) ) :
	function get_header ( $slug = NULL ) {
		return get_template_part ( 'header', $slug );
	}
endif;

if ( ! function_exists ( 'get_footer' ) ) :
	function get_footer ( $slug = NULL ) {
		return get_template_part ( 'footer', $slug );
	}
endif;

if ( ! function_exists ( 'get_sidebar' ) ) :
	function get_sidebar ( $slug = NULL ) {
		return get_template_part ( 'sidebar', $slug );
	}
endif;

if ( ! function_exists ( 'the_content' ) ) :
	function the_content () {
		$_ci =& get_instance();
		return $_ci->load->get_var ( 'content' );
	}
endif;

if ( ! function_exists ( 'get_content' ) ) :
	function get_content() {
		echo the_content();
	}
endif;

if ( ! function_exists ( 'get_template_part' ) ) :
	function get_template_part ( $name, $slug = NULL ) {
		$_ci =& get_instance();
		$theme_file = $name . ( is_null ( $slug ) ? NULL : '-' . $slug );
		$_ci->load->theme_part = $theme_file;
		if ( 'header' !== $name ) echo "\n";
		$_ci->load->theme();
		if ( 'footer' !== $name ) echo "\n";
	}
endif;

if ( ! function_exists ( 'get_template_directory_uri' ) ) :
	function get_template_directory_uri ( $filepath = NULL ) {
		$_ci =& get_instance();
		$uri_string = trim ( get_appinfo ( 'template_path ' ), '/' ) .'/'. trim ( $filepath, '/' );
		return $_ci->config->base_url ( get_appinfo ( 'template_path' ) . $filepath );
	}
endif;

if ( ! function_exists ( 'get_template_directory' ) ) :
	function get_template_directory ( $filepath = NULL ) {
		return FCPATH . trim ( get_appinfo ( 'template_path' ), '/' ) .'/'. trim ( $filepath, '/' );
	}
endif;

if ( ! function_exists ( 'get_stylesheet_uri' ) ) :
	function get_stylesheet_uri() {
		return get_appinfo ( 'stylesheet_url' );
	}
endif;

if ( ! function_exists( 'get_enqueue_style' ) ) :
	function get_enqueue_style ( $id, $file, $require = array(), $version = NULL ) {
		$_ci =& get_instance();
		$_ci->load->enqueue_style ( $id, $file, $require, $version );
	}
endif;

if ( ! function_exists( 'get_enqueue_script' ) ) :
	function get_enqueue_script ( $id, $file, $require = array(), $version = NULL, $in_footer = FALSE ) {
		$_ci =& get_instance();
		$_ci->load->enqueue_script ( $id, $file, $require, $version, $in_footer );
	}
endif;

if ( ! function_exists( 'theme_enqueue_head' ) ) :
	function theme_enqueue_head() {
		$_ci =& get_instance();
		$_ci->load->theme_enqueue_head();
	}
endif;

if ( ! function_exists( 'theme_enqueue_foot' ) ) :
	function theme_enqueue_foot() {
		$_ci =& get_instance();
		$_ci->load->theme_enqueue_foot();
	}
endif;

if ( ! function_exists ( 'app_title' ) ) :
	function app_title ( $sep = '&raquo;', $display = TRUE, $seplocation = 'left' ) {
		$_ci =& get_instance();

		$title = $_ci->load->get_var ( 'title' ) ? $_ci->load->get_var ( 'title' ) :
			ucwords ( str_replace ( '_', ' ', $ci->router->fetch_class() ) );

		$return = ( $seplocation === 'left' ? $sep . ' ' : NULL ) . $title . ( $seplocation === 'right' ? ' '.$sep : NULL );
		if ( ! $display ) return $return; echo $return;
	}
endif;

if ( ! function_exists ( 'get_appinfo' ) ) :
	function get_appinfo ( $info = 'name', $default = 'Codeigniter' ) {
		$_ci =& get_instance();
		$_ci->load->theme_initiate();
		$get_info = $_ci->load->get_var ( $info );
		return ( 'name' == $info AND ! $get_info ) ? $default : $get_info;
	}
endif;

if ( ! function_exists ( 'appinfo' ) ) :
	function appinfo ( $show = 'name' ) {
		echo get_appinfo($show);
	}
endif;