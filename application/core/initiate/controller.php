<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class MY_Controller extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}

	protected function &model ( $name, $alias = null, $config = array() ) {
		$var = is_null ( $alias ) ? $name : $alias;
		if ( isset ( $this->$name ) ) {
			return $this->name;
		} elseif ( isset ( $this->$alias ) ) {
			return $this->$alias;
		} else {}
		$this->load->model ( $name, $alias, $config );
		return $this->$var;
	}

	protected function &libs ( $name, $config = array(), $alias = null ) {
		$var = is_null ( $alias ) ? $name : $alias;
		if ( isset ( $this->$name ) ) {
			return $this->name;
		} elseif ( isset ( $this->$alias ) ) {
			return $this->$alias;
		} else {}
		$this->load->library ( $name, $config, $alias );
		return $this->$var;
	}
}