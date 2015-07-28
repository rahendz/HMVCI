<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class MY_Controller extends CI_Controller {

	public $ignored = array ( 'inheritance', 'test', 'sign' );
	private $aControllers;
	private $hideMethod = array();
	
	public function __construct(){
		parent::__construct();
		$this->hideMethod = array('views','model','getControllers','setControllerMethods','setControllers','render_theme','user_priviledge');
		$this->setControllers();
	}

	protected function &model ( $name ) {
		$this->load->model($name);
		return $this->$name;
	}

	public function getControllers ( $key = null ) {
		if ( ! is_null ( $key ) ) {
			return $this->aControllers[$key];
		}
		return $this->aControllers;
	}

	public function setControllerMethods($p_sControllerName, $p_aControllerMethods) {
		$this->aControllers[$p_sControllerName] = $p_aControllerMethods;
	}

	private function setControllers() {
		// Loop through the controller directory
		foreach(glob(APPPATH . 'controllers/*') as $controller) {
			
			// if the value in the loop is a directory loop through that directory
			if(is_dir($controller)) {
				// Get name of directory
				$dirname = basename($controller, EXT);
				
				// Loop through the subdirectory
				foreach(glob(APPPATH . 'controllers/'.$dirname.'/*') as $subdircontroller) {
					// Get the name of the subdir
					$subdircontrollername = basename($subdircontroller, EXT);
					
					// Load the controller file in memory if it's not load already
					if(!class_exists($subdircontrollername)) {				
						$this->load->file($subdircontroller);
					}					
					// Add the controllername to the array with its methods
					$aMethods = get_class_methods($subdircontrollername);
					$aUserMethods = array();
					foreach($aMethods as $method) {
						if($method != '__construct' && $method != 'get_instance' && $method != $subdircontrollername) {
							$aUserMethods[] = $method;
						}
					}
					$this->setControllerMethods($subdircontrollername, $aUserMethods);					 					
				}
			}
			else if(pathinfo($controller, PATHINFO_EXTENSION) == "php"){
				// value is no directory get controller name				
			    $controllername = basename($controller, EXT);
									
				// Load the class in memory (if it's not loaded already)
				if(!class_exists($controllername)) {
					$this->load->file($controller);
				}				
					
				// Add controller and methods to the array
				$aMethods = get_class_methods($controllername);
				$aUserMethods = array();
				if(is_array($aMethods)){
					foreach($aMethods as $method) {
						if($method != '__construct' && $method != 'get_instance' && $method != $controllername && ! in_array ( $method, $this->hideMethod ) && strpos($method, '__') === false ) {
							$aUserMethods[] = $method;
						}
					}
				}
									
				$this->setControllerMethods($controllername, $aUserMethods);								
			}
		}	
	}
}