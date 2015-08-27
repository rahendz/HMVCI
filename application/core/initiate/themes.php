<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! function_exists ( 'get_header' ) ) :
	function get_header ( $slug = null ) {
		return get_template_part ( 'header', $slug );
	}
endif;

if ( ! function_exists ( 'get_navbar' ) ) :
	function get_navbar ( $slug = null ) {
		return get_template_part ( 'navbar', $slug );
	}
endif;

if ( ! function_exists ( 'get_footer' ) ) :
	function get_footer ( $slug = null ) {
		return get_template_part ( 'footer', $slug );
	}
endif;

if ( ! function_exists ( 'get_sidebar' ) ) :
	function get_sidebar ( $slug = null ) {
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
	function get_template_part ( $name, $slug = null ) {
		$_ci =& get_instance();
		$theme_file = $name . ( is_null ( $slug ) ? null : '-' . $slug );
		$_ci->load->theme_part = $theme_file;
		if ( 'header' !== $name ) echo "\n";
		$_ci->load->theme();
		if ( 'footer' !== $name ) echo "\n";
	}
endif;

if ( ! function_exists ( 'get_template_directory_uri' ) ) :
	function get_template_directory_uri ( $filepath = null ) {
		$_ci =& get_instance();
		$uri_string = trim ( get_appinfo ( 'template_path ' ), '/' ) .'/'. trim ( $filepath, '/' );
		return $_ci->config->base_url ( get_appinfo ( 'template_path' ) . $filepath );
	}
endif;

if ( ! function_exists ( 'get_template_directory' ) ) :
	function get_template_directory ( $filepath = null ) {
		return FCPATH . trim ( get_appinfo ( 'template_path' ), '/' ) .'/'. trim ( $filepath, '/' );
	}
endif;

if ( ! function_exists ( 'get_stylesheet_uri' ) ) :
	function get_stylesheet_uri() {
		return get_appinfo ( 'stylesheet_url' );
	}
endif;

if ( ! function_exists( 'get_enqueue_style' ) ) :
	function get_enqueue_style ( $id, $file, $require = array(), $version = null ) {
		$_ci =& get_instance();
		$_ci->load->enqueue_style ( $id, $file, $require, $version );
	}
endif;

if ( ! function_exists( 'get_enqueue_script' ) ) :
	function get_enqueue_script ( $id, $file, $require = array(), $version = null, $in_footer = FALSE ) {
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
	function theme_enqueue_foot ( $return = null ) {
		$_ci =& get_instance();
		$_ci->load->theme_enqueue_foot ( $return );
	}
endif;

if ( ! function_exists ( 'app_title' ) ) :
	function app_title ( $sep = '&raquo;', $display = TRUE, $seplocation = 'left', $additional_sep = '&ndash;' ) {
		$_ci =& get_instance();
		$return = null;

		if ( ! $_ci->uri->segment(1) ) {
			return;
		}

		if ( $_ci->load->get_var ( 'title' ) ) {
			$title = $_ci->load->get_var ( 'title' );
		} elseif ( $_ci->load->get_var ( 'title_page' ) ) {
			$title = $_ci->load->get_var ( 'title_page' );
		} else {
			// $title = ucwords ( str_replace ( '-', ' ', end ( $_ci->uri->segment_array() ) ) );
			$_ci->load->model('m_posts','posts');
			$post = $_ci->posts->get_postdata();
			$title = $post['post_title'];
		}

		if ( $seplocation === 'left' AND ( ! is_null ( $title ) AND ! empty ( $title ) ) ) {
			$return = $sep .' '. $title;
		} elseif ( $seplocation === 'right' AND ( ! is_null ( $title ) AND ! empty ( $title ) ) ) {
			$return = $title .' '. $sep ;
		}

		if ( ! $display ) {
			return $return;
		}
		echo $return;
	}
endif;

if ( ! function_exists ( 'get_appinfo' ) ) :
	function get_appinfo ( $info = 'name' ) {
		$_ci =& get_instance();
		$_ci->load->theme_initiate();
		$get_info = $_ci->load->get_var ( $info );
		return ( 'name' == $info AND ! $get_info ) ? $_ci->config->item('app_name') : $get_info;
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
		if ( ! $_ci->posts->have_posts() ) {
			content();
			return false;
		}
		return $_ci->posts->have_posts();
	}
}

if ( ! function_exists ( 'the_post' ) ) {
	function the_post() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		return $_ci->posts->the_post();
	}
}

if ( ! function_exists ( 'is_protected' ) ) {
	function is_protected(){
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		return $_ci->posts->is_protected();
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

if ( ! function_exists ( 'get_logout_url' ) AND ! function_exists ( 'logout_url' ) AND ! function_exists ( 'logout_link') ) {
	function get_logout_url(){
		$_ci =& get_instance();
		return $_ci->config->site_url ( 'sign?out' );
	}
	function logout_url(){
		echo get_logout_url();
	}
	function get_logout_link ( $label = 'Logout', $class = null ){
		if ( ! is_null ( $class ) ) {
			$class = ' class="' .$class. '"';
		}
		return '<a href="' .get_logout_url(). '"' .$class. '>' .$label. '</a>';
	}
	function logout_link ( $label = 'Logout' ){
		echo get_logout_link ( $label );
	}
}

if ( ! function_exists ( 'get_login_url' ) AND ! function_exists ( 'login_url' ) AND ! function_exists ( 'login_link') ) {
	function get_login_url(){
		$_ci =& get_instance();
		$query = ( isset ( $_SERVER['QUERY_STRING'] ) AND ! empty ( $_SERVER['QUERY_STRING'] ) ) ? '?' . $_SERVER['QUERY_STRING'] : null;
		return $_ci->config->site_url ( 'sign?in&redirect=' . urlencode ( $_ci->config->site_url ( $_ci->uri->uri_string() . $query ) ) );
	}
	function login_url(){
		echo get_login_url();
	}
	function login_link ( $label = 'Login', $class = null ){
		if ( ! is_null ( $class ) ) {
			$class = ' class="' .$class. '"';
		}
		if ( is_logged_in() ) {
			echo '<li><a href="' .site_url('control'). '">Control Panel</a></li><li>' .get_logout_link('Log Out','btn btn-warning'). '</li>';
		} else {
			echo '<li><a href="' .get_login_url(). '"' .$class. '>' .$label. '</a></li>';
		}
	}
}

if ( ! function_exists ( 'is_home' ) ) {
	function is_home() {
		$_ci =& get_instance();
		if ( ! $_ci->uri->segment(1) ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists ( 'is_single' ) ) {
	function is_single() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		$postdata = $_ci->posts->get_postdata();
		if ( $postdata['post_type'] == 'post' ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists ( 'is_page' ) ) {
	function is_page() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		$postdata = $_ci->posts->get_postdata();
		if ( $postdata['post_type'] == 'page' ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists ( 'is_attachment' ) ) {
	function is_attachment() {
		$_ci =& get_instance();
		$_ci->load->model('m_posts','posts');
		$postdata = $_ci->posts->get_postdata();
		if ( $postdata['post_type'] == 'attachment' ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists ( 'navbar_menu' ) ) {
	function navbar_menu ( $str = null, $navbar = null ) {
		$echo = 1;
		$hover = 1;

		$_ci =& get_instance();

		$menus = json_decode ( get_option('menus')->values );
		$priviledges = json_decode ( get_option('menu_priviledges')->values );

		if ( current_logged_user() ) {
			$lid = current_logged_user()->lid;
		}

		$_ci->load->model('m_posts','posts');

		if ( is_array ( $str ) ) {
			$output = $str;
		} elseif ( ! is_null ( $str ) ) {
			parse_str ( $str, $output );
		}

		if ( isset ( $output ) ) {
			extract ( $output );
		}

		$page = $_ci->uri->segment(1);
		$sub = $_ci->uri->segment(2);
		$sup = $_ci->uri->segment(3);

		foreach ( $menus as $p ) {
			$pslug = isset ( $p->slug ) ? $p->slug : $p->linkid;
			if ( isset ( $p->children ) ) {
				if ( ! isset ( $priviledges->$pslug ) OR ( isset ( $lid ) AND in_array ( $lid, $priviledges->$pslug->level ) ) ) {
					$navbar .= '<li class="dropdown' . ( (bool) $hover ? ' hover' : null) . ( $page === $pslug ? ' active' : null ) . 
						'"><a href="' . ( isset ( $p->url ) ? str_replace ( '%root%', site_url(), $p->url ) : site_url() .'/'. $pslug ) . '" class="dropdown-toggle"' .( (bool) $hover ? null : ' data-toggle="dropdown"'). 
						'>' . $p->title . ' <span class="caret"></span></a>';

					$navbar .= '<ul class="dropdown-menu">';

					if ( (bool) $hover == false ) {
						$navbar .= '<li' . ( ( $page === $pslug AND ! $sub AND ! $sup ) ? ' class="active"' : null ) . '><a href="' . ( isset ( $p->url ) ? str_replace ( '%root%', site_url(), $p->url ) : site_url() .'/'. $pslug ) . '">Overview</a></li>';
					}

					foreach ( $p->children as $c ) {
						$cslug = $c->slug;
						if ( isset ( $c->children ) ) {
							if ( ! isset ( $priviledges->$pslug->$cslug ) OR ( isset ( $lid ) AND in_array ( $lid, $priviledges->$pslug->$cslug->level ) ) ) {
								$navbar .= '<li class="dropdown-submenu' . ( $sub === $cslug ? ' active' : null ) . '"><a href="' . ( isset ( $c->url ) ? str_replace ( '%root%', site_url(), $c->url ) : site_url() .'/'. $pslug .'/'. $cslug ) . '">' . $c->title . '</a>';

								$navbar .= '<ul class="dropdown-menu">';
								foreach ( $c->children as $g ) {
									$gslug = $g->slug;
									if ( ! isset ( $priviledges->$pslug->$cslug->$gslug ) OR ( isset ( $lid ) AND in_array ( $lid, $priviledges->$pslug->$cslug->$gslug->level ) ) ) {
										$navbar .= '<li' . ( $sup === $gslug ? ' class="active"' : null ) . '><a href="' . ( isset ( $g->url ) ? str_replace ( '%root%', site_url(), $g->url ) : site_url() .'/'. $pslug .'/'. $cslug .'/'. $gslug ) . '">' . $g->title . '</a>';
									}
								}
								$navbar .= '</ul>';
							}
						} else {
							if ( ! isset ( $priviledges->$pslug->$cslug ) OR ( isset ( $lid ) AND in_array ( $lid, $priviledges->$pslug->$cslug->level ) ) ) {
								$navbar .= '<li' . ( $page === $cslug ? ' class="active"' : null ) . '><a href="' . ( isset ( $s->url ) ? str_replace ( '%root%', site_url(), $s->url ) : site_url() .'/'. $pslug .'/'. $cslug ) . '">' . $c->title . '</a>';
							}
						}
					}

					$navbar .= '</ul>';
				}
			} else {
				if ( ! isset ( $priviledges->$pslug ) OR ( isset ( $lid ) AND in_array ( $lid, $priviledges->$pslug->level ) ) ) {
					$navbar .= '<li' . ( $page === $pslug ? ' class="active"' : null ) . '><a href="' . ( isset ( $p->url ) ? str_replace ( '%root%', site_url(), $p->url ) : site_url() .'/'. $pslug ) . '">' . $p->title . '</a>';
				}
			}

			$navbar .= '</li>';
		}

		if ( ! (bool) $echo ) {
			return $navbar;
		}

		echo $navbar;
	}
}

if ( ! function_exists ( 'get_admin_sidebar' ) ) {
	function get_admin_sidebar ( $index = false ) {
		$_ci =& get_instance();
		$control_home = $_ci->uri->segment(2) ? null : ' class="active"';
		$class = $_ci->router->fetch_class();
		$sidebar = '<li' .$control_home. '><a href="' .site_url($class). '">Home</a></li>';
		foreach ( $_ci->getControllers($class) as $item ) {
			if ( ! $index AND $item == 'index' ) {
				continue;
			}
			$control_active = $_ci->uri->segment(2)==$item?' class="active"':null;
			if ( is_priviledge_approved ( $item ) ) {
				$sidebar .= '<li' .$control_active. '><a href="' .site_url($class.'/'.$item). '">' .ucwords(str_replace('_',' ',$item)). '</a></li>';
			}
		}
		$sidebar .= '<li>' .get_logout_link(). '</li>';
		// echo_r(current_logged_user());
		// echo_r(the_last_query());
		return $sidebar;
	}
}

if ( ! function_exists ( 'admin_sidebar' ) ) {
	function admin_sidebar ( $index = false ) {
		echo get_admin_sidebar ( $index );
	}
}

if ( ! function_exists ( 'get_parent_posts' ) ) {
	function get_parent_posts ( $pid ) {
		$_ci =& get_instance();
		$_ci->load->model('m_posts');
		return $_ci->m_posts->get_parent ( $pid );
	}
}

if ( ! function_exists ( 'get_slug_posts' ) ) {
	function get_slug_posts ( $pid ) {
		$_ci =& get_instance();
		$_ci->load->model('m_posts');
		return $_ci->m_posts->get_slug_by_id ( $pid );
	}
}

if ( ! function_exists ( 'get_title_posts' ) ) {
	function get_title_posts ( $pid ) {
		$_ci =& get_instance();
		$_ci->load->model('m_posts');
		return $_ci->m_posts->get_title_by_id ( $pid );
	}
}

if ( ! function_exists ( 'get_parent_id' ) ) {
	function get_parent_id ( $pid ) {
		$_ci =& get_instance();
		$_ci->load->model('m_posts');
		return $_ci->m_posts->get_parent_id ( $pid );
	}
}