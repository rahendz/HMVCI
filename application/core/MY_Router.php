<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class MY_Router extends CI_Router {
	var $module = '';

	function __construct() {
		$this->config =& load_class ( 'Config', 'core' );

		$locations = $this->config->item ( 'modules_locations' );

		if ( ! $locations ) {
			$locations = array ( APPPATH . 'modules/' => '../modules/' );
		}

		elseif ( ! is_array ( $locations ) ) {
			$locations = array ( $locations );
			$this->config->set_item ( 'modules_locations', $locations );
		}

		foreach ( $locations as &$location ) {
			$location = realpath ( $location );
			$location = str_replace ( '\\', '/', $location );
			$location = rtrim ( $location, '/' ) . '/';
		}

		parent::__construct();
	}

	function _validate_request ( $segments ) {
		if ( count ( $segments ) == 0 ) {
			return $segments;
		}

		if ( $located = $this->locate ( $segments ) ) {
			return $located;
		}

		if ( ! empty ( $this->routes['404_override'] ) ) {
			$segments = explode ( '/', $this->routes['404_override'] );
			if ( $located = $this->locate ( $segments ) ) {
				return $located;
			}
		}

		echo 'MY_Router : 46'; show_404 ( $segments[0] );
	}

	function _parse_routes() {
		$segstart = ( intval ( substr ( CI_VERSION, 0, 1 ) ) > 2 ) ? 1 : 0;

		if ( $module = $this->uri->segment ( $segstart ) ) {
			foreach ( $this->config->item ( 'modules_locations' ) as $location => $realpath ) {
				if ( is_file ( $file = $location . $module . '/config/routes.php' ) ) {
					include ( $file );

					$route = ( ! isset ( $route ) OR ! is_array ( $route ) ) ? array() : $route;
					$this->routes = array_merge ( $this->routes, $route );
					unset ( $route );
				}
			}
		}

		return parent::_parse_routes();
	}

	function locate ( $segments )
	{
		$_ucfirst = function ( $cn ) { return ( intval ( substr ( CI_VERSION, 0, 1 ) ) > 2 ) ? ucfirst ( $cn ) : $cn; };
		list ( $module, $directory, $controller ) = array_pad ( $segments, 3, NULL );
		foreach ( $this->config->item ( 'modules_locations' ) as $location => $relative ) {
			// $relative = $location;
			$start = rtrim ( realpath ( APPPATH ), '/' );
			$parts = explode ( '/', str_replace ( '\\', '/', $start ) );

			for ( $i = 1; $i <= count ( $parts ); $i++ )
			{
				$relative = str_replace ( implode ( '/', $parts ) . '/', str_repeat ( '../', $i ), $relative, $count );
				array_pop ( $parts );
				if ( $count ) break;
			}
			
			if ( is_dir ( $source = $location . $module . '/controllers/' ) )
			{
				$this->module = $module;
				$this->directory = $relative . $module . '/controllers/';
				if ( $directory && is_file ( $source . $_ucfirst ( $directory ) . '.php' ) )
				{
					$this->class = $directory;
					return array_slice ( $segments, 1 );
				}
				if ( $directory && is_dir ( $source . $directory . '/' ) )
				{
					$source = $source . $directory . '/';
					$this->directory .= $directory . '/';
					if ( is_file ( $source . $_ucfirst ( $directory ) . '.php' ) )
					{
						return array_slice ( $segments, 1 );
					}
					if ( is_file ( $source . $_ucfirst ( $this->default_controller ) . '.php' ) )
					{
						$segments[1] = $this->default_controller;
						return array_slice ( $segments, 1 );
					}

					// Module sub-directory sub-controller?
					if ($controller && is_file ( $source . $_ucfirst ( $controller ) . '.php' ) )
					{
						return array_slice ( $segments, 2 );
					}
				}

				if ( is_file ( $source . $_ucfirst ( $module ) . '.php' ) )
				{
					return $segments;
				}

				if ( is_file ( $source . $_ucfirst ( $this->default_controller ) . '.php' ) )
				{
					$segments[0] = $this->default_controller;
					return $segments;
				}
			}
		}

		if ( $directory && is_file ( APPPATH . 'controllers/' . $module . '/' . $_ucfirst ( $directory ) . '.php' ) )
		{
			$this->directory = $module . '/';
			return array_slice ( $segments, 1 );
		}

		if ( is_file ( APPPATH . 'controllers/' . $_ucfirst ( $module ) . '.php' ) )
		{
			return $segments;
		}

		if ( is_file ( APPPATH . 'controllers/' . $module . '/' . $_ucfirst ( $this->default_controller ) . '.php' ) )
		{
			$segments[0] = $this->default_controller;
			return $segments;
		}
	}

	function set_module ( $module )
	{
		$this->module = $module;
	}

	public function _set_default_controller()
	{
		parent::_set_default_controller();
		$class = $this->fetch_class();
		if ( empty ( $class ) ) {
			if ( sscanf ( $this->default_controller, '%[^/]/%s', $class, $method ) !== 2 )
			{
				$method = 'index';
			}
			if ( $located = $this->locate ( array ( $class, $class, $method ) ) )
			{
				log_message('debug', 'No URI present. Default module controller set.');
			}
		}
	}

	function fetch_module()
	{
		return $this->module;
	}
	function set_directory ( $dir )
	{
		$this->directory = $dir;
	}
	function _set_overrides ( $routing ) {
		if ( ! is_array ( $routing ) ) {
			return;
		}

		extract ( $routing );

		if ( isset ( $directory ) ) {
			$slash = empty ( $directory ) ? null : '/';
			$class = ( isset ( $controller ) AND ! empty ( $controller ) ) ? $controller : $this->class;

			if ( is_dir ( MODULESPATH . $class . '/controllers/' . $directory ) ) {
				$dir = '../../' . MODULESPATH . $class . '/controllers/' . $directory . $slash;
			} else {
				$dir = str_replace ( array ( '/', '.' ), '', $directory ) . $slash;
			}

			$this->set_directory ( $dir );
		} else {
			$class = ( isset ( $controller ) AND ! empty ( $controller ) ) ? $controller : $this->class;
			if ( file_exists ( MODULESPATH . $class . '/controllers/' . $class . '.php' ) ) {
				$this->set_directory ( '../../' . MODULESPATH . $class . '/controllers/' );
			}
		}

		if ( isset ( $controller ) AND $controller != '' ) {
			$this->set_class ( $controller );
			$this->module = $controller;
		}

		if ( isset ( $function ) ) {
			$function = ( $function == '' ) ? 'index' : $function;
			$this->set_method ( $function );
		}
	}
}