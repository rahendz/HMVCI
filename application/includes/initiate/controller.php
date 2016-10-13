<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class Base_Controller extends CI_Controller {

	public $ignored = array('inheritance', 'test', 'sign');

	protected $theme = 'default';

	protected $theme_var = array();

	private $aControllers;

	private $hideMethod = array();

	public function __construct(){
		parent::__construct();
		$this->hideMethod = array('views', 'model', 'getControllers', 'setControllerMethods', 'setControllers', 'render_theme', 'user_priviledge');
		$this->setControllers();
	}

	public function getControllers ( $key = null ) {
		if ( ! is_null ( $key ) ) {
			return $this->aControllers[$key];
		}
		return $this->aControllers;
	}

	public function setControllerMethods ($p_sControllerName, $p_aControllerMethods) {
		$this->aControllers[$p_sControllerName] = $p_aControllerMethods;
	}

	private function setControllers() {
		// Loop through the controller directory
		foreach (glob(APPPATH . 'controllers/*') as $controller) {

			// if the value in the loop is a directory loop through that directory
			if (is_dir($controller)) {
				// Get name of directory
				$dirname = basename($controller, EXT);

				// Loop through the subdirectory
				foreach (glob(APPPATH . 'controllers/'.$dirname.'/*') as $subdircontroller) {
					// Get the name of the subdir
					$subdircontrollername = basename($subdircontroller, EXT);

					// Load the controller file in memory if it's not load already
					if (!class_exists($subdircontrollername)) {
						$this->load->file($subdircontroller);
					}
					// Add the controllername to the array with its methods
					$aMethods = get_class_methods($subdircontrollername);
					$aUserMethods = array();
					foreach ($aMethods as $method) {
						if ($method != '__construct' && $method != 'get_instance' && $method != $subdircontrollername) {
							$aUserMethods[] = $method;
						}
					}
					$this->setControllerMethods($subdircontrollername, $aUserMethods);
				}
			}
			elseif (pathinfo($controller, PATHINFO_EXTENSION) == "php"){
				// value is no directory get controller name
			    $controllername = basename($controller, EXT);

				// Load the class in memory (if it's not loaded already)
				if (!class_exists($controllername)) {
					$this->load->file($controller);
				}

				// Add controller and methods to the array
				$aMethods = get_class_methods($controllername);
				$aUserMethods = array();
				if (is_array($aMethods)){
					foreach ($aMethods as $method) {
						if ($method != '__construct' && $method != 'get_instance' && $method != $controllername && ! in_array ( $method, $this->hideMethod ) && strpos($method, '__') === false ) {
							$aUserMethods[] = $method;
						}
					}
				}

				$this->setControllerMethods($controllername, $aUserMethods);
			}
		}
	}

	protected function enqueue_style( $id, $file = null, $require = array(), $version = null ) {
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

	protected function enqueue_script( $id, $file = null, $require = array(), $version = null, $in_footer = false ) {
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

	protected function controller($class) {
		return $this->load->controller($class);
	}

	protected function model ($name, $rename=null) {
		$this->load->model($name, $rename);
		if (!is_null($rename)) {
			$name = $rename;
		}
		return $this->$name;
	}

	protected function library ($name, $rename=null, $param=null) {
		$this->load->library($name, $param, $rename);
		if (!is_null($rename)) {
			$name = $rename;
		}
		return $this->$name;
	}

	protected function view ($view, $vars=array(), $return=false) {
		return $this->load->view($view, $vars, $return);
	}

	protected function file ($view, $vars=array(), $return=false) {
		return $this->load->file($view, $vars, $return);
	}

	protected function config ($file='', $use_sections=false, $fail_gracefully=false) {
		return $this->load->config($file, $use_sections, $fail_gracefully);
	}

	protected function database ($params='', $return=false, $active_record=null) {
		return $this->load->database($params, $return, $active_record);
	}

	protected function helper ($helper = array()) {
		return $this->load->helper($helper);
	}

	protected function language ($file=array(), $lang='') {
		return $this->load->language($file, $lang);
	}

	protected function render_theme($vars = array(), $return = false) {
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