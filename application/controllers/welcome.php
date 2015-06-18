<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Public_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index()
	{
		// echo $this->load->current_controller; exit;
		// get_enqueue_style ( 'style', 'style.css', array(), '1.0.0' );
		$this->enqueue_style = array (
			// array ( 'test', 'test.css', array(), '1.0.0' ),
			// array ( 'telo', 'telo.css', array('test'), '1.2.0' ),
			'style' => array ( 'style.css', array(), '1.0.0' )
			);

		$this->enqueue_script = array (
			// array ( 'jquery', 'jquery.js', array(), '1.0.0' ),
			// array ( 'asik', 'asik.js',array('jquery'),'1.2.0',TRUE),
			'meneh' => array ( 'meneh.js',array(),'3.2.0', TRUE )
			);

		// $wel = new M_Welcome;
		// echo_r ( $model->test() );
		// $wel =& load_model ( 'm_welcome' );
		// $wel->sign_validation();
		// echo_r( $wel->test() );
		// echo_r( $wel->test_2() );
		// $this->data_theme['jinguk'] = 'parsing data to theme';

		// $theme_data['content'] = $this->load->view ( 'welcome_message', array(), TRUE );
		$this->content_theme = 'test_form';
		// $this->content_theme = 'welcome_message';
		return $this->render_theme ( /*$theme_data*/ );
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */