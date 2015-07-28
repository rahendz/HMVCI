<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! function_exists ( 'logged_in' ) ) {
	function logged_in ( $redirect = 'admin' ) {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_users', 'users' );
		return $_ci->users->logged_in ( $redirect );
	}
}

if ( ! function_exists ( 'current_logged_user' ) ) {
	function current_logged_user() {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_users', 'users' );
		return $_ci->users->current_logged_user();
	}
}

if ( ! function_exists ( 'is_logged_in' ) ) {
	function is_logged_in ( $redirect = false ) {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_users', 'users' );
		return $_ci->users->is_logged_in ( $redirect );
	}
}

if ( ! function_exists ( 'is_logged_as' ) ) {
	function is_logged_as ( $level ) {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_users', 'users' );
		return $_ci->users->is_logged_as ( $level );
	}
}

if ( ! function_exists ( 'is_user_level' ) ) {
	function is_user_level ( $level ) {
		$current = current_logged_user();
		if ( ( is_array ( $level ) AND in_array ( $current->type, $level ) ) OR $current->type == $level ) {
			return true;
		}
		show_error ( 'You don\'t have permission to access this page. Contact Administrator for more help.' );
		return false;
	}
}

if ( ! function_exists ( 'is_priviledge_approved' ) ) {
	function is_priviledge_approved ( $method = null ) {
		$_ci =& get_instance();
		$_ci->load->model ( 'm_settings', 'settings' );
		return $_ci->settings->is_priviledge_approved ( $method );
	}
}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */