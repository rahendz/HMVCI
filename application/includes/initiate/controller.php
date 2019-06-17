<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class Base_Controller extends CI_Controller {

	public $ignored = array('inheritance', 'test', 'sign');

	protected $theme;

	protected $theme_var = array();

	private $order = 0;

	private $controllers;

	private $hide_method = array();

	public function __construct(){
		parent::__construct();
		// $this->hideMethod = array('views', 'model', 'getControllers', '__set_controller_methods', 'setControllers', 'render_theme', 'user_priviledge');
		$this->__set_controllers();
	}

	public function __get_controllers ( $key = null, $type = false ) {
		if (!is_null($key)) {
			return $this->controllers[$key];
		}
		if (is_array($this->controllers) && count($this->controllers) > 0) {
			if ($type!=false) {
				for($i=0;$i<count($this->controllers);$i++) {
					$types[] = $type;
				}
				$controllers = array_flatten(array_filter(array_map(function($c,$d,$t){
					$parent = get_parent_class($c);
					if (strpos($parent, ucfirst($t))===0 || strpos($parent, 'CI')===0) {
						return array($c=>$d);
					}
				}, array_keys($this->controllers), $this->controllers, $types)));
				return $controllers;
			}
			return $this->controllers;
		}
		return false;
	}

	public function __set_controller_methods ($controller_name, $controller_methods) {
		if (!isset($controller_methods['menu']['order'])) {
			$controller_methods['menu']['order'] = $this->order;
		}
		$this->controllers[$controller_name] = $controller_methods;
		$this->order = $this->order + 5;
	}

	private function __set_controllers() {
		// Loop through the controller directory
		foreach (glob(APPPATH . 'controllers/*') as $controller) {

			// if the value in the loop is a directory loop through that directory
			if (is_dir($controller)) {
				// Get name of directory
				$dirname = basename($controller, EXT);

				// Loop through the subdirectory
				foreach (glob(APPPATH . 'controllers/'.$dirname.'/*') as $sub_dir_controller) {
					$this->__reg_controllers($sub_dir_controller);
				}

			}
			elseif (pathinfo($controller, PATHINFO_EXTENSION) == "php") {
				$this->__reg_controllers($controller);
			}
		}

		$modules_path = key ( $this->config->item ( 'modules_locations' ) );

		foreach ( glob ( $modules_path . '*/controllers/*' ) as $controller ) {
			if ( is_dir ( $controller ) ) {
				$dirname = basename ( $controller, '.php' );

				foreach ( glob ( $modules_path . '*/controllers/'.$dirname.'/*' ) as $sub_controller ) {
					$this->__reg_controllers ( $sub_controller );
				}
			}

			elseif ( pathinfo ( $controller, PATHINFO_EXTENSION ) == 'php' ) {
				$this->__reg_controllers ( $controller );
			}
		}
	}

	private function __reg_controllers($controller) {
		// Get the name of the subdir
		$controller_name = basename($controller, EXT);

		// If class not already called
		if (!class_exists($controller_name)) {
			// Then called it
			$this->load->file($controller);
		}

		// Get methods and vars from current class
		$methods = get_class_methods($controller_name);
		$objects = get_class_vars ( $controller_name );

		// Initiate methods memory
		$user_methods = array();
		// When methods exists
		if (is_array($methods)) {
			// Initiate method order
			$order = 0;
			// Loop it for filtering methods
			foreach ($methods as $method) {
				// Filter method
				if (strpos($method, '__') !== 0 && strpos($method, 'get_instance') !== 0 && $method !== $controller_name) {
					// If there is menu variable
					if (!isset($user_methods['menu']) && isset($objects['menu'])) {
						// Then register current class as menu
						$user_methods['menu'] = $objects['menu'];
					}
					// If menu title not set yet
					if (!isset($user_methods['menu']['title'])) {
						// Then registered one
						$user_methods['menu']['title'] = ucwords(str_replace('_',' ',$controller_name));
					}
					// If menu subtitle not set yet
					if (!isset($user_methods['menu']['subtitle'])) {
						// Then registered one
						$user_methods['menu']['subtitle'] = $user_methods['menu']['title'];
					}
					// If submenu variable exist
					if (!isset($user_methods['submenu']) && isset($objects['submenu'])) {
						// Then register it
						$user_methods['submenu'] = $objects['submenu'];
					}
					// If each method submenu title not set yet
					if (!isset($user_methods['submenu'][$method]['title'])) {
						// Then set each method submenu
						$user_methods['submenu'][$method]['title'] = ucwords(str_replace('_', ' ', $method));
					}
					// If each method submenu order not set yet
					if (!isset($user_methods['submenu'][$method]['order'])) {
						// Then register now
						$user_methods['submenu'][$method]['order'] = $order;
						// Arrange order by 5 for development purpose
						$order = $order + 5;
					}
				}
			}
		}
		// Register all user method and variable into declared class
		$this->__set_controller_methods($controller_name, $user_methods);
	}

	protected function __enqueue_style( $id, $file = null, $require = array(), $version = null ) {
		if(!is_array($id)){
			return $this->load->enqueue_style( $id, $file, $require, $version );
		}
		foreach($id as $style_id => $param){
			for($i=3;$i>count($param);$i--){
				$param[] = null;
			}
			$this->load->enqueue_style($style_id,$param[0],$param[1],$param[2]);
		}
	}

	protected function __enqueue_script( $id, $file = null, $require = array(), $version = null, $in_footer = false ) {
		if(!is_array($id)) {
			return $this->load->enqueue_script( $id, $file, $require, $version, $in_footer );
		}
		foreach($id as $script_id => $param){
			for($i=4;$i>count($param);$i--){
				$param[] = null;
			}
			$this->load->enqueue_script($script_id,$param[0],$param[1],$param[2],$param[3]);
		}
	}

	protected function __controller($class) {
		return $this->load->controller($class);
	}

	protected function __model ($name, $rename=null) {
		if ((is_null($rename) && !isset($this->name)) || (!is_null($rename) && !isset($this->$rename))) {
			$this->load->model($name, $rename);
		}
		if (!is_null($rename)) {
			$name = $rename;
		}
		return $this->$name;
	}

	protected function __library ($name, $rename=null, $param=null) {
		if ((!is_null($rename) && !isset($this->$rename)) || (is_null($rename) && !isset($this->$name))) {
			$this->load->library($name, $param, $rename);
		}
		if (!is_null($rename)) {
			$name = $rename;
		}
		return $this->$name;
	}

	protected function __view ($view, $vars=array(), $return=false) {
		$this->load->view($view, $vars, $return);
	}

	protected function __file ($view, $vars=array(), $return=false) {
		$this->load->file($view, $vars, $return);
	}

	protected function __config ($file='', $use_sections=false, $fail_gracefully=false) {
		return $this->load->config($file, $use_sections, $fail_gracefully);
	}

	protected function __database ($params='', $return=false, $active_record=null) {
		return $this->load->database($params, $return, $active_record);
	}

	protected function __helper ($helper = array()) {
		return $this->load->helper($helper);
	}

	protected function __language ($file=array(), $lang='') {
		return $this->load->language($file, $lang);
	}

	protected function __render_theme($vars = array(), $return = false) {
		if (isset($this->theme_var['config'])) {
			$this->load->theme_config = $this->theme_var['config'];
		}
		if (isset($this->theme_var['data'])) {
			$this->load->views_data = $this->theme_var['data'];
		}
		if (isset($this->theme_var['content'])) {
			$this->load->views_file = $this->theme_var['content'];
		}
		if (isset($this->theme_var['part'])) {
			$this->load->theme_part = $this->theme_var['part'];
		}
		return $this->load->theme ( $vars, $return );
	}
}