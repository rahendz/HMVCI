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

if ( ! function_exists ( 'get_content' ) ) :
	function get_content () {
		$_ci =& get_instance();
		return $_ci->load->get_var ( 'content' );
	}
endif;

if ( ! function_exists ( 'content' ) ) :
	function content() {
		echo get_content();
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
	function theme_enqueue_head ( $return = null ) {
		$_ci =& get_instance();
		$_ci->load->theme_enqueue_head ( $return );
	}
endif;

if ( ! function_exists( 'theme_enqueue_foot' ) ) :
	function theme_enqueue_foot ( $return = null) {
		$_ci =& get_instance();
		$_ci->load->theme_enqueue_foot ( $return );
	}
endif;

if ( ! function_exists ( 'app_title' ) ) :
	function app_title ( $sep = '&raquo;', $display = TRUE, $seplocation = 'left' ) {
		$_ci =& get_instance();

		$title = $_ci->load->get_var ( 'title' ) ? $_ci->load->get_var ( 'title' ) :
			ucwords ( str_replace ( '_', ' ', $ci->router->fetch_class() ) );

		$title_page = $_ci->load->get_var ( 'title_page' ) ? $_ci->load->get_var ( 'title_page' ) : NULL;

		if ( is_null ( $title_page ) ) {
			$sepleft = $sepright = NULL;
		} elseif ( $seplocation === 'left' ) {
			$sepleft = $title_page .' '. $sep .' ';
			$sepright = NULL;
		} elseif ( $seplocation === 'right' ) {
			$sepright = ' '. $sep .' '. $title_page ;
			$sepleft = NULL;
		} else {
			$sepleft = $sepright = NULL;
		}

		$return = $sepleft . $title . $sepright;
		if ( ! $display ) {
			return $return;
		}
		echo $return;
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

if ( ! function_exists ( 'have_posts' ) ) {
	function have_posts() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		return $_ci->posts->have_posts();
		$content = $_ci->posts->get_page_content ( $_ci->uri->segment(1), $_ci->uri->segment(2), $_ci->uri->segment(3) );
		// return $content;
		$jinguk = 'tenan';
		if ( ! $content ) {
			return FALSE;
		} elseif ( $content->protected == '1' ) {
			echo 'content are protected';
			return FALSE;
		}
		return $content;
	}
}

if ( ! function_exists ( 'the_post' ) ) {
	function the_post() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		return $_ci->posts->the_post();
	}
}

if ( ! function_exists ( 'query_posts' ) ) {
	function query_posts ( $query_string ) {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		parse_str ( $query_string, $output );
		foreach ( $output as $vars => $val ) {
			$_ci->posts->$vars = $val;
		}
		// return $_ci->posts->query_post ( $query_string );
	}
}

if ( ! function_exists ( 'get_the_title' ) AND ! function_exists ( 'the_title' ) ) {
	function get_the_title() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		// echo_r(have_posts(),TRUE);
		return $_ci->posts->get_the_title();
	}
	function the_title() {
		echo get_the_title();
	}
}

if ( ! function_exists ( 'get_the_content' ) AND ! function_exists ( 'the_content' ) ) {
	function get_the_content() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		// echo_r(have_posts(),TRUE);
		return $_ci->posts->get_the_content();
	}
	function the_content() {
		echo get_the_content();
	}
}

if ( ! function_exists ( 'get_the_excerpt' ) AND ! function_exists ( 'the_excerpt' ) ) {
	function get_the_excerpt() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		// echo_r(have_posts(),TRUE);
		return $_ci->posts->get_the_excerpt();
	}
	function the_excerpt() {
		echo get_the_excerpt();
	}
}

if ( ! function_exists ( 'the_last_query' ) ) {
	function the_last_query() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		echo $_ci->posts->the_last_query();
	}
}