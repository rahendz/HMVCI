<?php

class Autoloader {

	protected $prefixes = array();

	protected static $instance;

	public function __construct()
	{
		$this->prefixes['core'] = APPPATH . 'core/';
	}

	public static function getInstance()
	{
		if ( ! isset ( static::$instance ) ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	public function register() {
		spl_autoload_register ( array ( $this, 'loadClass' ) );
	}

	public function unregister() {
		spl_autoload_unregister ( array ( $this, 'loadClass' ) );
	}

	public function addPrefix ( $namespace, $path ) {
		$this->prefixes[$namespace] = $path;
	}

	public function loadClass ( $class ) {
		foreach ( $this->prefixes as $prefix => $path ) {
			if ( strpos ( $class, $prefix ) === 0 ) {
				$class_path = $path . str_replace ( '\\', '/', substr (  $class, strlen ( $prefix ) ) ) . '.php';
				if ( file_exists ( $class_path ) ) {
					require $class_path;
					return;
				}
			}
		}
	}
}