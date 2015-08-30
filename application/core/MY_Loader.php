<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

	protected $_ci;
	protected $_ci_modules = array();
	protected $_ci_controllers = array();

	public $theme_config = array();
	public $theme_part = null;
	public $theme_dir = null;
	public $views_file = null;
	public $views_data = array();

	public $enqueue_style = array();
	public $enqueue_style_id = array();
	public $enqueue_script = array();
	public $enqueue_script_id = array();

	public $current_controller = null;
	public $current_module_path = null;

	public function __construct() {
		parent::__construct();
		$router =& $this->_ci_get_component ( 'router' );
		if ( $router->module ) {
			$this->add_module ( $router->module );
		}
		$this->current_module_path = $this->get_current_module_path();
		$this->current_controller = $this->get_current_controller();
		$this->_ci =& get_instance();
	}

	public function controller ( $uri, $params = array(), $return = false ) {
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

	public function library ( $library = '', $params = null, $object_name = null ) {
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

	public function model ( $model, $name = '', $db_conn = false ) {
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

	public function view ( $view, $vars = array(), $return = false ) {
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

	public function file ( $path, $vars = array(), $return = false ) {
		return $this->_ci_load ( array (
			'_ci_path' => $path,
			'_ci_vars' => $this->_ci_object_to_array ( $vars ),
			'_ci_return' => $return
			) );
	}

	public function config ( $file = '', $use_sections = false, $fail_gracefully = false ) {
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

	public function database ( $params = '', $return = FALSE, $active_record = NULL ) {
		$router =& $this->_ci_get_component ( 'router' );

		if ( empty ( $router->module ) ) {
			return parent::database ( $params, $return, $active_record );
		}

		// Do we even need to load the database class?
		if ( class_exists ( 'CI_DB' ) AND $return == FALSE AND $active_record == NULL AND isset ( $_ci->db ) AND is_object ( $_ci->db ) )
		{
			return FALSE;
		}

		// require_once(BASEPATH.'database/DB.php');

		if ( $return === TRUE )
		{
			return $this->_ci_get_database ( $params, $active_record );
		}

		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		$_ci->db = '';

		// Load the DB class
		$_ci->db =& $this->_ci_get_database ( $params, $active_record );
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

	// THEME ROUTING
	public function theme_initiate() {
		$router =& $this->_ci_get_component ( 'router' );

		$backend_theme_path = config_item ( 'backend_theme_path' ) != false ?
			FCPATH . config_item ( 'backend_theme_path' ) : APPPATH .'themes/';

		$frontend_theme_path = config_item ( 'frontend_theme_path' ) != false ?
			FCPATH . config_item ( 'frontend_theme_path' ) : FCPATH . 'themes/';

		if ( isset ( $this->theme_config['frontend'] ) ) {
			$theme_name = $this->theme_config['frontend'];
			$this->theme_dir = is_dir ( $frontend_theme_path . $theme_name ) ?
				$frontend_theme_path . $theme_name . '/' : array (
					'dir' => $frontend_theme_path . $theme_name );
		} elseif ( $this->theme_config['backend'] ) {
			$theme_name = $this->theme_config['backend'];
			$this->theme_dir = is_dir ( $backend_theme_path . $theme_name ) ?
				$backend_theme_path . $theme_name . '/' : array (
					'dir' => $backend_theme_path . $theme_name );
		}

		! is_array ( $this->theme_dir ) OR show_error ( '<p><strong>' . strtoupper ( key ( $this->theme_config ) ) .
			' THEME NOTICE:</strong> It\'s seems theme directory aren\'t set yet or missing.</p><code>' .
			str_replace ( '/', '\\', $this->theme_dir['dir'] ) . '</code>' );

		$config =& $this->_ci_get_component ( 'config' );

		$this->vars ( array (
			'base_url' => $config->base_url(),
			'site_url' => $config->site_url(),
			'template_type' => key ( $this->theme_config ),
			'template_path' => str_replace ( FCPATH, '', $this->theme_dir ),
			'stylesheet_url' => $config->base_url ( str_replace ( FCPATH, '', $this->theme_dir ) . 'style.css' )
			) );

		// if ( ! isset ( $this->views_file ) AND is_null ( $this->theme_part ) ) {
		// 	show_error ( '<p><strong>THEME NOTICE:</strong> It\'s seems theme content aren\'t set properly or it\'s missing.</p>' );
		// }
		$path_view = $config->_config_paths[0] . 'views/';

		if ( is_null ( $this->views_file ) OR empty ( $this->views_file ) ) {
			if ( $router->method == 'index' AND is_file ( $path_view . $router->class . '/main' . EXT ) ) {
				$this->views_file = $router->class . '/main';
			} elseif ( is_file ( $path_view . $router->class . '/' . $router->method . EXT ) ) {
				$this->views_file = $router->class . '/' . $router->method;
			} elseif ( is_file ( $path_view . $router->class . EXT ) ) {
				$this->views_file = $router->class;
			}
		} elseif ( is_file ( $path_view . $router->class . '/' . $this->views_file . EXT ) ) {
			$this->views_file = $router->class . '/' . $this->views_file;
		} elseif ( is_file ( $path_view . $this->views_file . '/main' . EXT ) ) {
			$this->views_file = $this->views_file . '/main';
		}
	}

	public function theme ( $vars = array(), $_ci_return = false ) {
		$this->theme_initiate();

		if ( ! is_null ( $this->views_file ) AND $this->views_file !== false ) {
			$vars = array_merge ( $vars, array (
				'content' => $this->view ( $this->views_file, $this->views_data, true )
				) );
		}

		$_ci_vars = $this->_ci_object_to_array ( $vars );
		$theme_files = array ( 'front-page', 'blog', 'home', 'index' );

		if ( ! is_null ( $this->theme_part ) ) {
			$theme_files = array ( $this->theme_part );
			extract ( $this->views_data );
		}

		foreach ( $theme_files as $index_file ) {
			if ( file_exists ( $index_path = $this->theme_dir . $index_file . EXT ) !== false ) break;
			else $index_path = false;
		}

		( $index_path !== false ) OR show_error ( '<p><strong>' . strtoupper ( key ( $this->theme_config ) ) .
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
		if ( (bool) @ini_get ( 'short_open_tag' ) === false AND config_item ( 'rewrite_short_tags' ) == true ) :
			echo eval ( '?>' . preg_replace ( "/;*\s*\?>/", "; ?>", str_replace ( '<?=', '<?php echo ', file_get_contents ( $index_path ) ) ) );
		else : include ( $index_path ); endif;

		log_message ( 'debug', 'File loaded: '.$index_path );

		if ( $_ci_return === true ) {
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

	public function enqueue_style ( $id, $file = null, $require = array(), $version = null ) {
		$this->enqueue_style[$id] = array (
			'file' => $file,
			'require' => $require,
			'ver' => $version
			);
	}

	public function enqueue_script ( $id, $file = null, $require = array(), $version = null, $in_footer = false ) {
		$this->enqueue_script[$id] = array (
			'file' => $file,
			'require' => $require,
			'ver' => $version,
			'in_footer' => $in_footer
			);
	}

	public function theme_enqueue_head ( $return = null ) {
		$assets_css_path = APPPATH . 'core/assets/css/';
		$assets_js_path = APPPATH . 'core/assets/js/';

		$return .= '<meta name="site_url" content="'. $this->config->site_url() .'" />'. "\n\t";
		$return .= '<meta name="assets_path" content="'. $this->config->base_url ( APPPATH . 'core/assets' ) .'" />'. "\n\t";
		$return .= '<meta name="template_directory_uri" content="'. get_template_directory_uri() .'" />'. "\n\t";
		$return .= '<meta name="stylesheet_url" content="'. get_stylesheet_uri() .'" />'. "\n\t";

		foreach ( $this->enqueue_style as $id => $e ) {
			list ( $file, $require, $version ) = $e;

			$requires = isset ( $require ) ? $require : array();
			$filepath = get_template_directory_uri ( $file . '?ver=' . ( isset ( $version ) ? $version : null ) );
			$rel = 'stylesheet';

			if ( strpos ( $file, 'http://' ) !== false ) {
				$filepath = $file . '?ver=' . ( isset ( $version ) ? $version : NULL );
			}

			if ( strpos($file, '.less')!==false){
				$rel = 'stylesheet/less';
			}
			if ( $this->is_anystyle_required ( $requires ) === false ) {
				foreach ( $requires as $r ) {
					$r = file_exists( $assets_css_path . $r . '.min.css' ) ? $r . '.min' : $r;
					if ( file_exists ( $assets_css_path . $r . '.css' ) ) {
						$require_file = base_url ( $assets_css_path . $r . '.css' );
						$return .= sprintf ( '<link rel="%s" id="%s-css" href="%s" />',	$rel, $r, $require_file ) . "\n\t";
						$this->enqueue_style_id[] = $r;
					}
				}
			}

			if ( $id !== 'style' AND $this->is_anystyle_required ( $requires ) !== false ) {
				$return .= sprintf ( '<link rel="%s" id="%s-css" href="%s" />',	$rel, $id, $filepath ) . "\n\t";
				$this->enqueue_style_id[] = $id;
			} else {
				$stylesheet = sprintf ( '<link rel="%s" id="%s-css" href="%s" />', $rel, $id, $filepath ) . "\n\t";
			}
		}

		if ( isset ( $stylesheet ) ) {
			$return .= $stylesheet;
		}

		$force_load_last = array (
			'trigger', 'admin-trigger', 'trigger-admin', 'trigger-backend', 'backend-trigger', 'trigger-frontend', 'frontend-trigger'
			);

		foreach ( $this->enqueue_script as $id => $s ) {
			for ( $i = 4; $i > count($s); $i-- ) {
				$s[] = '';
			}

			list ( $file, $require, $version, $in_footer ) = $s;

			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : null ) );

			if ( strpos ( $file, 'http://' ) !== false ){
				$filepath = $file .'?ver='. ( isset ( $version ) ? $version : NULL );
			}

			$requires = isset ( $require ) ? $require : array();
			$for_footer = isset ( $in_footer ) ? $in_footer : false;

			if ( $this->is_anyscript_required ( $requires ) === false ) {
				foreach ( $requires as $r ) {

					$x = $r === 'tinymce' ? 'tinymce/tinymce' : $r;
					$re = file_exists ( $assets_js_path . $x . '.min.js' ) ? $x . '.min' : $x;

					if ( ( $r == 'jquery' OR $r == 'tinymce' ) AND file_exists ( $assets_js_path . $re . '.js' ) ) {
						$require_file = base_url ( $assets_js_path . $re . '.js' );
						if ( $this->is_anyscript_required ( array ( $r ) ) === false ) {
							$return .= sprintf ( '<script id="%s" src="%s"></script>', $r, $require_file ) . "\n\t";
							$this->enqueue_script_id[] = $r;
						}
					}
				}
			}

			if ( ! $for_footer AND ! in_array ( $id, $force_load_last ) AND $this->is_anyscript_required ( $require ) !== false ) {
				$return .= sprintf ( '<script id="%s" src="%s"></script>', $id, $filepath ) ."\n\t";
				$this->enqueue_script_id[] = $id;
			}
		}

		$return .= '<!--[if lt IE 9]>' . "\n\t";
		$return .= "\t" . sprintf ( '<script src="%s"></script>', base_url ( $assets_js_path . 'html5shiv.js' ) ) . "\n\t";
		$return .= "\t" . sprintf ( '<script src="%s"></script>', base_url ( $assets_js_path . 'respond.js' ) ) . "\n\t";
		$return .= '<![endif]-->' . "\n\t";

		echo rtrim ( $return, "\t" );
	}

	public function theme_enqueue_foot ( $return = "\n" ) {
		$assets_js_path = APPPATH . 'core/assets/js/';

		$force_load_last = array (
			'trigger', 'admin-trigger', 'trigger-admin', 'trigger-backend', 'backend-trigger', 'trigger-frontend', 'frontend-trigger'
			);

		foreach ( $this->enqueue_script as $id => $e ) {
			for ( $i = 4; $i > count($e); $i-- ) {
				$e[] = '';
			}

			list ( $file, $require, $version, $in_footer ) = $e;

			$filepath = get_template_directory_uri ( $file .'?ver='. ( isset ( $version ) ? $version : null ) );

			if ( strpos ( $file, 'http://' ) !== false ){
				$filepath = $file .'?ver='. ( isset ( $version ) ? $version : NULL );
			}

			$requires = isset ( $require ) ? $require : array();
			$for_footer = isset ( $in_footer ) ? $in_footer : false;

			if ( $this->is_anyscript_required ( $requires ) === false ) {
				foreach ( $requires as $r ) {

					// $x = $r === 'tinymce' ? 'tinymce/tinymce' : $r;
					$re = file_exists ( $assets_js_path . $r . '.min.js' ) ? $r . '.min' : $r;

					// echo $assets_js_path . $re . '<br/>';
					if ( $r !== 'jquery' AND file_exists ( $assets_js_path . $re . '.js' ) ) {
						$require_file = base_url ( $assets_js_path . $re . '.js' );
						$return .= sprintf ( '<script id="%s" src="%s"></script>',	$r, $require_file ) . "\n";
						$this->enqueue_script_id[] = $r;
					}
				}
			}
			if ( $for_footer AND ! in_array ( $id, $force_load_last ) AND $this->is_anyscript_required ( $requires ) !== false ) {
				$return .= sprintf ( '<script id="%s" src="%s"></script>', $id, $filepath ) . "\n";
				$this->enqueue_script_id[] = $id;
			} else {
				$stylescript = sprintf ( '<script id="%s" src="%s"></script>', $id, $filepath ) . "\n";
			}
		}

		if ( isset ( $stylescript ) ) {
			$return .= $stylescript;
		}

		echo $return;
	}

	public function get_component ( $type ) {
		return $this->_ci_get_component ( $type );
	}

	private function is_anystyle_required ( $require = array() ) {
		foreach ( $require as $r ) {
			if ( ! isset ( $this->enqueue_style_id ) OR ! in_array ( $r, $this->enqueue_style_id ) ) {
				return false;
			}
		}
		return true;
	}

	private function is_anyscript_required ( $require = array() ) {
		foreach ( $require as $r ) {
			if ( ! in_array ( $r, $this->enqueue_script_id ) ) {
				return false;
			}
		}
		return true;
	}

	/* PRIVATE FUNCTION */
	private function add_module ( $module, $view_cascade = true ) {
		if ( $path = $this->find_module ( $module ) ) {
			array_unshift ( $this->_ci_modules, $module );
			parent::add_package_path ( $path, $view_cascade );
		}
	}

	private function remove_module ( $module = '', $remove_config = true ) {
		if ( $module == '' ) {
			array_shift ( $this->_ci_modules );
			parent::remove_package_path ( '', $remove_config );
		}
		elseif ( ( $key = array_search ( $module, $this->_ci_modules ) ) !== false ) {
			if ( $path = $this->find_module ( $module ) ) {
				unset ( $this->_ci_modules[$key] );
				parent::remove_package_path($path, $remove_config);
			}
		}
	}

	private function _load_controller ( $uri = '', $params = array(), $return = false ) {
		$router = & $this->_ci_get_component ( 'router' );
		$backup = array();
		foreach ( array ( 'directory', 'class', 'method', 'module' ) as $prop ) {
			$backup[$prop] = $router->{$prop};
		}
		$segments = $router->locate ( explode ( '/', $uri ) );
		$class = isset ( $segments[0] ) ? $segments[0] : false;
		$method = isset ( $segments[1] ) ? $segments[1] : "index";
		if ( ! $class ) return;
		if ( ! array_key_exists ( strtolower ( $class ), $this->_ci_controllers ) ) {
			if ( file_exists ( APPPATH . 'controllers/' . $router->fetch_directory() . $class . '.php' ) )
				include_once ( APPPATH . 'controllers/' . $router->fetch_directory() . $class . '.php' );
			elseif ( file_exists ( APPPATH . 'controllers/' . $class . '.php' ) )
				include_once ( APPPATH . 'controllers/' . $class . '.php' );
			if ( ! class_exists ( $class ) ) show_404 ( "MY Loader:528 {$class}/{$method}" );
			$this->_ci_controllers[strtolower($class)] = new $class();
		}
		$controller = $this->_ci_controllers[strtolower($class)];
		if ( ! method_exists ( $controller, $method ) ) show_404 ( "MY Loader:532 {$class}/{$method}" );
		foreach ( $backup as $prop => $value ) $router->{$prop} = $value;
		ob_start();
		$result = call_user_func_array ( array ( $controller, $method ), $params );
		if ( $return === true ) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		ob_end_flush();
		return $result;
	}

	private function detect_module ( $class ) {
		$class = str_replace ( '.php', '', trim ( $class, '/' ) );
		if ( ( $first_slash = strpos ( $class, '/' ) ) !== false ) {
			$module = substr ( $class, 0, $first_slash );
			$class = substr ( $class, $first_slash + 1 );
			if ( $this->find_module ( $module ) ) return array ( $module, $class );
		}
		if ( $this->find_module ( $class ) ) return array ( $class );
		return false;
	}

	private function find_module ( $module ) {
		$config =& $this->_ci_get_component ( 'config' );
        foreach ( $config->item ( 'modules_locations' ) as $location => $realpath ) {
            $path = $location . rtrim ( $module, '/' ) . '/';
            if ( is_dir ( $path ) ) {
                return $path;
            }
        }
        return FALSE;
	}

	private function get_current_module_path() {
		return current ( $this->get_package_paths() );
	}

	private function get_current_controller() {
		// return $this->get_package_paths();
		$router = & $this->_ci_get_component ( 'router' );
		// $config = & $this->_ci_get_component ( 'config' );
		// return $config;
		return $this->current_module_path . 'controllers/' . $router->class . EXT;
		// return $controller_path;
		// return APPPATH . 'controllers/' . $router->directory . $router->class . EXT;
	}

	private function &_ci_get_database ( $params = '', $active_record_override = NULL ) {
		$router =& $this->_ci_get_component ( 'router' );
		$config =& $this->_ci_get_component ( 'config' );

		// Load the DB config file if a DSN string wasn't passed
		if ( is_string ( $params ) AND strpos ( $params, '://' ) === FALSE )
		{
			// Is the config file in the environment folder?
			if ( ! defined('ENVIRONMENT') OR
				! file_exists ( $file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php' ) OR
				! file_exists ( $file_path = $config->_config_paths[0] . 'config/database.php' ) )
			{
				if ( ! file_exists ( $file_path = APPPATH . 'config/database.php' ) OR ! file_exists ( $file_path = $config->_config_paths[0] . 'config/database.php' ) ) {
					show_error ( 'MY Loader:583 The configuration file database.php does not exist. ' .$file_path );
				}
			}

			include ( $file_path );

			if ( ! isset ( $db ) OR count ( $db ) == 0 )
			{
				show_error('No database connection settings were found in the database config file.');
			}

			if ( $params != '' )
			{
				$active_group = $params;
			}

			if ( ! isset ( $active_group ) OR ! isset ( $db[$active_group] ) )
			{
				show_error ( 'You have specified an invalid database connection group.' );
			}

			$params = $db[$active_group];
		}
		elseif ( is_string ( $params ) )
		{

			/* parse the URL from the DSN string
			 *  Database settings can be passed as discreet
			 *  parameters or as a data source name in the first
			 *  parameter. DSNs must have this prototype:
			 *  $dsn = 'driver://username:password@hostname/database';
			 */

			if ( ( $dns = @parse_url ( $params ) ) === FALSE )
			{
				show_error ( 'Invalid DB Connection String' );
			}

			$params = array (
								'dbdriver'	=> $dns['scheme'],
								'hostname'	=> ( isset ( $dns['host'] ) ) ? rawurldecode ( $dns['host'] ) : '',
								'username'	=> ( isset ( $dns['user'] ) ) ? rawurldecode ( $dns['user'] ) : '',
								'password'	=> ( isset ( $dns['pass'] ) ) ? rawurldecode ( $dns['pass'] ) : '',
								'database'	=> ( isset ( $dns['path'] ) ) ? rawurldecode ( substr ( $dns['path'], 1 ) ) : ''
							);

			// were additional config items set?
			if ( isset ( $dns['query'] ) )
			{
				parse_str ( $dns['query'], $extra );

				foreach ( $extra as $key => $val )
				{
					// booleans please
					if ( strtoupper ( $val ) == "TRUE" )
					{
						$val = TRUE;
					}
					elseif ( strtoupper ( $val ) == "FALSE" )
					{
						$val = FALSE;
					}

					$params[$key] = $val;
				}
			}
		}

		// No DB specified yet?  Beat them senseless...
		if ( ! isset ( $params['dbdriver'] ) OR $params['dbdriver'] == '' )
		{
			show_error ( 'You have not selected a database type to connect to.' );
		}

		// Load the DB classes.  Note: Since the active record class is optional
		// we need to dynamically create a class that extends proper parent class
		// based on whether we're using the active record class or not.
		// Kudos to Paul for discovering this clever use of eval()

		if ( $active_record_override !== NULL )
		{
			$active_record = $active_record_override;
		}

		require_once ( BASEPATH . 'database/DB_driver.php' );

		if ( ! isset ( $active_record ) OR $active_record == TRUE )
		{
			require_once ( BASEPATH . 'database/DB_active_rec.php' );

			if ( ! class_exists ( 'CI_DB' ) )
			{
				eval ( 'class CI_DB extends CI_DB_active_record { }' );
			}
		}
		else
		{
			if ( ! class_exists ( 'CI_DB' ) )
			{
				eval ( 'class CI_DB extends CI_DB_driver { }' );
			}
		}

		require_once ( BASEPATH.'database/drivers/' . $params['dbdriver'] . '/' . $params['dbdriver'] . '_driver.php');

		// Instantiate the DB adapter
		$driver = 'CI_DB_' . $params['dbdriver'] . '_driver';
		$DB = new $driver ( $params );

		if ( $DB->autoinit == TRUE )
		{
			$DB->initialize();
		}

		if ( isset ( $params['stricton'] ) && $params['stricton'] == TRUE )
		{
			$DB->query ( 'SET SESSION sql_mode="STRICT_ALL_TABLES"' );
		}

		return $DB;
	}

}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */