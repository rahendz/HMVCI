<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class MY_Loader extends CI_Loader {

	protected $_ci_modules = array();
	protected $_ci_controllers = array();

	public $theme_part = NULL;
	public $theme_dir = NULL;
	public $views_files = NULL;
	public $views_data = array();

	public $enqueue_style = array();
	static $enqueue_style_id = array();
	public $enqueue_script = array();
	static $enqueue_script_id = array();

	public $current_controller = NULL;

	public function __construct() {
		parent::__construct();
		$router =& $this->_ci_get_component ( 'router' );
		if ( $router->module ) $this->add_module ( $router->module );
		$this->current_controller = $this->get_current_controller();
	}

	public function controller ( $uri, $params = array(), $return = FALSE ) {

		list ( $module ) = $this->detect_module ( $uri );
		if ( ! isset ( $module ) ) {
			$router =& $this->_ci_get_component ( 'router' );
			if ( $router->module ) {
				$module = $router->module;
				$uri = $module . '/' . $uri;
			}
		}
		else {
			$uri = implode ( '/', $this->detect_module ( $uri ) );
		}
		$this->add_module ( $module );
		$void = $this->_load_controller ( $uri, $params, $return );
		$this->remove_module();
		return $void;
	}

	public function library ( $library = '', $params = NULL, $object_name = NULL ) {
		if ( is_array ( $library ) ) {
			foreach ( $library as $lib ) {
				$this->library ( $lib, $params );
			}
			return;
		}

		if ( list ( $module, $lib ) = $this->detect_module ( $library ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::library ( $lib, $params, $object_name );
			}
			$this->add_module ( $module );
			$void = parent::library ( $lib, $params, $object_name );
			$this->remove_module();
			return $void;
		}

		else {
			return parent::library ( $library, $params, $object_name );
		}
	}

	public function model ( $model, $name = '', $db_conn = FALSE ) {
		if ( is_array ( $model ) ) {
			foreach ( $model as $mod ) {
				$this->model ( $mod );
			}
			return;
		}

		if ( list ( $module, $class ) = $this->detect_module ( $model ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::model ( $class, $name, $db_conn );
			}
			$this->add_module ( $module );
			$void = parent::model ( $class, $name, $db_conn );
			$this->remove_module();
			return $void;
		}

		else {
			return parent::model ( $model, $name, $db_conn );
		}
	}

	public function view ( $view, $vars = array(), $return = FALSE ) {
		if ( $this->detect_module ( $view ) && list ( $module, $class ) = $this->detect_module ( $view ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::view ( $class, $vars, $return );
			}
			$this->add_module ( $module );
			$void = parent::view ( $class, $vars, $return );
			$this->remove_module();
			return $void;
		}

		else {
			return parent::view ( $view, $vars, $return );
		}
	}

	public function file ( $path, $vars = array(), $return = FALSE ) {
		return $this->_ci_load ( array (
			'_ci_path' => $path,
			'_ci_vars' => $this->_ci_object_to_array ( $vars ),
			'_ci_return' => $return
			) );
	}

	public function config ( $file = '', $use_sections = FALSE, $fail_gracefully = FALSE ) {
		if ( list ( $module, $class ) = $this->detect_module ( $file ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::config ( $class, $use_sections, $fail_gracefully );
			}
			$this->add_module ( $module );
			$void = parent::config ( $class, $use_sections, $fail_gracefully );
			$this->remove_module();
			return $void;
		}

		else {
			parent::config ( $file, $use_sections, $fail_gracefully );
		}
	}

	public function helper ( $helper = array() ) {
		if ( is_array ( $helper ) ) {
			foreach ( $helper as $help ) {
				$this->helper ( $help );
			}
			return;
		}

		if ( list ( $module, $class ) = $this->detect_module ( $helper ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::helper ( $class );
			}
			$this->add_module ( $module );
			$void = parent::helper ( $class );
			$this->remove_module();
			return $void;
		}

		else {
			return parent::helper ( $helper );
		}
	}

	public function language ( $file = array(), $lang = '' ) {
		if ( is_array ( $file ) ) {
			foreach ( $file as $langfile ) {
				$this->language ( $langfile, $lang );
			}
			return;
		}

		if ( list ( $module, $class ) = $this->detect_module ( $file ) ) {
			if ( in_array ( $module, $this->_ci_modules ) ) {
				return parent::language ( $class, $lang );
			}
			$this->add_module ( $module );
			$void = parent::language ( $class, $lang );
			$this->remove_module();
			return $void;
		}

		else {
			return parent::language ( $file, $lang );
		}
	}

	public function theme_initiate() {

		$this->config ( 'themes' );
		$router =& $this->_ci_get_component ( 'router' );

		$theme_config = config_item ( 'theme-name' );
		$theme_data = isset ( $theme_config[$router->method] ) ?
			$theme_config[$router->method] : $theme_config;

		$backend_theme_path = config_item ( 'backend_theme_path' ) !== FALSE ?
			FCPATH . config_item ( 'backend_theme_path' ) : FCPATH . APPPATH .'themes/';

		$frontend_theme_path = config_item ( 'frontend_theme_path' ) !== FALSE ?
			FCPATH . config_item ( 'frontend_theme_path' ) : FCPATH . 'themes/';

		switch ( key ( $theme_data ) ) :
			default:
			case 'frontend':
				$theme_name = isset ( $theme_data['frontend'] ) ? $theme_data['frontend'] : 'default';
				$this->theme_dir = is_dir ( $frontend_theme_path . $theme_name ) ?
					$frontend_theme_path . $theme_name .'/' :
					array ( 'dir' => $frontend_theme_path . $theme_name );
				break;

			case 'backend':
				$theme_name = isset ( $theme_data['backend'] ) ? $theme_data['backend'] : 'default';
				$this->theme_dir = is_dir ( $backend_theme_path . $theme_name ) ?
					$backend_theme_path . $theme_name . '/' :
					array ( 'dir' => $backend_theme_path . $theme_name );
				break;
		endswitch;

		! is_array ( $this->theme_dir ) OR show_error ( '<p><strong>' . strtoupper ( key ( $theme_data ) ) .
			' THEME NOTICE:</strong> It\'s seems theme directory aren\'t set yet or missing.</p><code>' .
			str_replace ( '/', '\\', $this->theme_dir['dir'] ) . '</code>' );

		$config =& $this->_ci_get_component ( 'config' );

		$this->vars ( array (
			'base_url' => $config->base_url(),
			'site_url' => $config->site_url(),
			'template_type' => key ( $theme_data ),
			'template_path' => str_replace ( FCPATH, '', $this->theme_dir ),
			'stylesheet_url' => $config->base_url ( str_replace ( FCPATH, '', $this->theme_dir ) . 'style.css' )
			) );
	}

	public function theme ( $vars = array(), $_ci_return = FALSE ) {

		$this->theme_initiate();

		if ( ! is_null ( $this->views_file ) ) {
			$vars = array_merge ( $vars, array (
				'content' => $this->view ( $this->views_file, $this->views_data, TRUE )
				) );
		}

		$_ci_vars = $this->_ci_object_to_array ( $vars );

		$theme_files = array ( 'front-page', 'blog', 'home', 'index' );
		if ( ! is_null ( $this->theme_part ) ) $theme_files = array ( $this->theme_part );

		foreach ( $theme_files as $index_file )
			if ( file_exists ( $index_path = $this->theme_dir . $index_file . EXT ) !== FALSE ) break;
			else $index_path = FALSE;

		( $index_path !== FALSE ) OR show_error ( '<p><strong>' . strtoupper ( key ( $theme_data ) ) .
			' THEME NOTICE:</strong> There\'s no index file on your theme.</p>' );

		! file_exists ( $theme_functions = $this->theme_dir . 'functions' . EXT ) OR @include_once $theme_functions;

		$_ci_CI =& get_instance();

		foreach ( get_object_vars ( $_ci_CI ) as $_ci_key => $_ci_var )
			if ( ! isset ( $this->$_ci_key ) ) $this->$_ci_key = $_ci_CI->$_ci_key;

		if ( is_array ( $_ci_vars ) AND count ( $_ci_vars ) > 0 )
			$this->_ci_cached_vars = array_merge ( $this->_ci_cached_vars, $_ci_vars );

		$this->vars ( $this->_ci_cached_vars );
		extract ( $this->_ci_cached_vars );

		ob_start();
		if ( (bool) @ini_get ( 'short_open_tag' ) === FALSE AND config_item ( 'rewrite_short_tags' ) == TRUE ) :
			echo eval ( '?>' . preg_replace ( "/;*\s*\?>/", "; ?>", str_replace ( '<?=', '<?php echo ', file_get_contents ( $index_path ) ) ) );
		else : include ( $index_path ); endif;

		log_message ( 'debug', 'File loaded: '.$index_path );

		if ( $_ci_return === TRUE ) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		if ( ob_get_level() > $this->_ci_ob_level + 1 ) {
			ob_end_flush();
		}

		else {
			$_ci_CI->output->append_output ( ob_get_contents() );
			@ob_end_clean();
		}
	}

	public function enqueue_style ( $id, $file, $require = array(), $version = NULL ) {
		$this->enqueue_style[$id] = array (
			'id' => $id,
			'file' => $file,
			'require' => $require,
			'ver' => $version
			);
	}

	public function enqueue_script ( $id, $file, $require = array(), $version = NULL, $in_footer = FALSE ) {
		$this->enqueue_script[$id] = array (
			'id' => $id,
			'file' => $file,
			'require' => $require,
			'ver' => $version,
			'in_footer' => $in_footer
			);
	}

	public function theme_enqueue_head ( $return = NULL ) {

		foreach ( $this->enqueue_style as $e ) {
			list ( $id, $file, $require, $version ) = $e;

			$requires = isset ( $require ) ? $require : array();
			$filepath = get_template_directory_uri ( $file . '?ver=' . ( isset ( $version ) ? $version : NULL ) );

			if ( $id !== 'style' AND $this->is_anystyle_required ( $requires ) !== FALSE ) {
				$return .= sprintf ( '<link rel="stylesheet" id="%s-css" href="%s" />',	$id, $filepath ) . "\n\t";
				$this->enqueue_style_id[] = $id;
			}
		}

		$is_style_found = recursive_array_search ( 'style', $this->enqueue_style );
		if ( $is_style_found !== FALSE ) {
			list ( $id, $file, $require, $version ) = $is_style_found;
			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : NULL ) );
			$return .= sprintf ( '<link rel="stylesheet" id="%s-css" href="%s" />', $id, $filepath ) ."\n\t";
		}

		$force_load_last = array (
			'trigger', 'admin-trigger', 'trigger-admin', 'trigger-backend', 'backend-trigger', 'trigger-frontend', 'frontend-trigger'
			);

		foreach ( $this->enqueue_script as $s ) {
			for ( $i = 5; $i > count($s); $i-- ) {
				$s[] = '';
			}

			list ( $id, $file, $require, $version, $in_footer ) = $s;

			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : NULL ) );
			$requires = isset ( $require ) ? $require : array();
			$for_footer = isset ( $in_footer ) ? $in_footer : FALSE;

			if ( ! $for_footer AND ! in_array ( $id, $force_load_last ) AND $this->is_anyscript_required ( $require ) !== FALSE ) {
				$return .= sprintf ( '<script id="%s" src="%s" ></script>', $id, $filepath ) ."\n\t";
				$this->enqueue_script_id[] = $id;
			}
		}

		echo rtrim ( $return, "\t" );
	}

	public function theme_enqueue_foot ( $return = "\n" ) {
		$force_load_last = array (
			'trigger', 'admin-trigger', 'trigger-admin', 'trigger-backend', 'backend-trigger', 'trigger-frontend', 'frontend-trigger'
			);

		foreach ( $this->enqueue_script as $e ) {
			for ( $i = 5; $i > count($e); $i-- ) {
				$e[] = '';
			}

			list ( $id, $file, $require, $version, $in_footer ) = $e;

			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : NULL ) );
			$requires = isset ( $require ) ? $require : array();
			$for_footer = isset ( $in_footer ) ? $in_footer : FALSE;

			if ( $for_footer AND ! in_array ( $id, $force_load_last ) AND $this->is_anyscript_required ( $requires ) !== FALSE ) {
				$return .= sprintf ( '<script id="%s" src="%s"></script>', $id, $filepath ) . "\n";
				$this->enqueue_script_id[] = $id;
			}
		}

		$is_trigger_found = recursive_array_search ( 'trigger', $this->enqueue_script );
		if ( $is_trigger_found !== FALSE ) {
			for ( $i = 5; $i > count($is_trigger_found); $i-- ) {
				$is_trigger_found[] = '';
			}

			list ( $id, $file, $require, $version, $in_footer ) = $is_trigger_found;

			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : NULL ) );

			$return .= sprintf ( '<script id="%s" src="%s"></script>', $id, $filepath ) . "\n";
		}

		echo $return;
	}

	/* PRIVATE FUNCTION */
	private function add_module ( $module, $view_cascade = TRUE ) {
		if ( $path = $this->find_module ( $module ) ) {
			array_unshift ( $this->_ci_modules, $module );
			parent::add_package_path ( $path, $view_cascade );
		}
	}

	private function remove_module ( $module = '', $remove_config = TRUE ) {
		if ( $module == '' ) {
			array_shift ( $this->_ci_modules );
			parent::remove_package_path ( '', $remove_config );
		}

		elseif ( ( $key = array_search ( $module, $this->_ci_modules ) ) !== FALSE ) {
			if ( $path = $this->find_module ( $module ) ) {
				unset ( $this->_ci_modules[$key] );
				parent::remove_package_path($path, $remove_config);
			}
		}
	}

	private function _load_controller ( $uri = '', $params = array(), $return = FALSE ) {
		$router = & $this->_ci_get_component ( 'router' );

		$backup = array();
		foreach ( array ( 'directory', 'class', 'method', 'module' ) as $prop ) {
			$backup[$prop] = $router->{$prop};
		}

		$segments = $router->locate ( explode ( '/', $uri ) );
		$class = isset ( $segments[0] ) ? $segments[0] : FALSE;
		$method = isset ( $segments[1] ) ? $segments[1] : "index";

		if ( ! $class ) return;

		if ( ! array_key_exists ( strtolower ( $class ), $this->_ci_controllers ) ) {
			if ( file_exists ( APPPATH . 'controllers/' . $router->fetch_directory() . $class . '.php' ) )
				include_once ( APPPATH . 'controllers/' . $router->fetch_directory() . $class . '.php' );
			elseif ( file_exists ( APPPATH . 'controllers/' . $class . '.php' ) )
				include_once ( APPPATH . 'controllers/' . $class . '.php' );
			if ( ! class_exists ( $class ) ) echo '390'; show_404 ( "{$class}/{$method}" );
			$this->_ci_controllers[strtolower($class)] = new $class();
		}

		$controller = $this->_ci_controllers[strtolower($class)];
		if ( ! method_exists ( $controller, $method ) ) echo '395';show_404 ( "{$class}/{$method}" );
		foreach ( $backup as $prop => $value ) $router->{$prop} = $value;

		ob_start();
		$result = call_user_func_array ( array ( $controller, $method ), $params );

		if ( $return === TRUE ) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		ob_end_flush();
		return $result;
	}

	private function detect_module ( $class ) {
		$class = str_replace ( '.php', '', trim ( $class, '/' ) );

		if ( ( $first_slash = strpos ( $class, '/' ) ) !== FALSE ) {
			$module = substr ( $class, 0, $first_slash );
			$class = substr ( $class, $first_slash + 1 );
			if ( $this->find_module ( $module ) ) return array ( $module, $class );
		}

		if ( $this->find_module ( $class ) ) return array ( $class );
		return FALSE;
	}

	private function find_module ( $module ) {
		$config =& $this->_ci_get_component ( 'config' );
		$module_path = current ( $config->item ( 'modules_locations' ) ) . rtrim ( $module, '/' ) . '/';
		$controllers_path = APPPATH . 'controllers/';

		if ( is_dir ( $module_path ) ) return $module_path;
		elseif ( is_dir ( $controllers_path . $module ) ) return $controllers_path . $module;
		elseif ( is_file ( $controllers_path . $module . EXT ) ) return $controllers_path;

		return FALSE;
	}

	private function is_anystyle_required ( $require = array() ) {
		foreach ( $require as $r ) {
			if ( ! in_array ( $r, $this->enqueue_style_id ) ) {
				return FALSE;
			}
		}
		return TRUE;
	}

	private function is_anyscript_required ( $require = array() ) {
		foreach ( $require as $r ) {
			if ( ! in_array ( $r, $this->enqueue_script_id ) ) {
				return FALSE;
			}
		}
		return TRUE;
	}

	private function get_current_controller() {
		$router = & $this->_ci_get_component ( 'router' );
		if ( empty ( $router->module ) ) {
			return APPPATH . 'controllers/' . $router->directory . $router->class . EXT;
		}
		$config =& $this->_ci_get_component ( 'config' );
		$real_modules_path = realpath ( current ( (array) $config->config['modules_locations'] ) );
		$modules_path = str_replace ( array ( FCPATH, '\\' ), array ( '', '/' ), $real_modules_path );
		return $modules_path .'/'. $router->module .'/controllers/'. realpath ( $router->directory ) . $router->class . EXT;
	}
}