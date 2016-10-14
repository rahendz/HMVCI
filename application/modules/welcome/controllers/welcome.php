<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Public_Controller {

	public function __construct() {
		parent::__construct();
		// $this->theme_var['config']['frontend'] = 'default';
	}

	public function index() {
		$this->theme_var['content'] = 'welcome_message';
		return $this->render_theme();
	}

	public function testdb() {
		# using direct model class
		## get one record
		$welcome = new Model;
		$welcome->table = 'balita_master';
		__e('GET ONE');
		__r($welcome->get_one());
		## get all records
		$welcome->table = 'product_nutrisi';
		__e('GET ALL');
		__r($welcome->get_all());
		# using app model loader
		$welcome2 = $this->model('m_welcome');
		__e('TEST');
		__r($welcome2->test());
		# using codeigniter method
		$this->load->model('m_welcome');
		__e('TEST MENEH');
		__r($this->m_welcome->test_again());
	}

	public function testfunc() {
		__r(__fields_data('products',array('Field'=>'id')));
	}

	public function testpaging() {
		$paging = __set_pagination('m_welcome',array('per_page'=>10));
		__r($paging);
	}

	public function testgetdoc() {
		$this->theme_var['content'] = 'welcome_message';
		return $this->render_theme();
	}

	public function testapi() {
		// $this->load->config('rest_api');
		// echo_r(config_item('api_logins'));
		$api = 'http://codeigniter.dev/hmvci/rahendz/index.php/api/';
		$value['secret'] = '65bb14860bff913a5cff848b5e6abc79e031350c';
		echo get_remote ( $api . 'example/users', $value );
		// echo config_item ( 'api_base_url' );
	}

	public function request() {
		$api = 'http://codeigniter.dev/hmvci/rahendz/index.php/api/';
		echo put_remote($api . 'key/index',array('keys'=>'rahendz'));
	}

	public function testmail() {
		$this->load->library('email');

                // $config = array(
                   // 'protocol' => 'smtp',
                    // 'smtp_host' => 'localhost',
                    // 'smtp_port' => '465',
                    // 'smtp_user' => 'j3ramb@gmail.com',
                    // 'smtp_pass' => 'pitung70'
                // );

                // Set your email information
                $from = array('email' => 'postmaster@localhost', 'name' => 'Rahendra Putra K');
                $to = array('rahendz@gmail.com');
                $subject = 'testing';

                $message = 'Type your gmail message here';
                // Load CodeIgniter Email library
                $this->load->library('email');

                // Sometimes you have to set the new line character for better result
                $this->email->set_newline("rn");
                // Set email preferences
                $this->email->from($from['email'], $from['name']);
                $this->email->to($to);

                $this->email->subject($subject);
                $this->email->message($message);
                // Ready to send email and check whether the email was successfully sent

                if (!$this->email->send()) {
                    // Raise error message
                    show_error($this->email->print_debugger());
                }
                else {
                    // Show success notification or other things here
                    echo 'Success to send email';
                }
	}

	public function testdownload() {
		$this->load->helper('download');
		$data = file_get_contents('http://simpus.uad.ac.id/nfs_spfile/sp_file/file_penelitian/T1_07007007_JUDUL.pdf');
		$name = 'T1_07007007_JUDUL.pdf';
		force_download($name,$data);
	}

	public function testdatatable() {
		$this->enqueue_style ( array (
			// 'style' => array ( 'style.css', array(), '1.0.0' ),
			'datatable' => array ( 'css/jquery.dataTables.min.css', array(), '1.10.9' )
			));
		// $this->enqueue_style('style','style.css',array(),'1.0.0');

		$this->enqueue_script ( array (
					'datatable' => array ( 'js/jquery.dataTables.js', array ('jquery-2.2.4'), '1.10.9', true ),
					'trigger' => array ( 'trigger.js',array(),'1.0.0', true )
					));

		$this->theme_var['content'] = 'welcome_datatable';
		return $this->render_theme();
	}

	public function data_record() {
		$draw = intval( $_REQUEST['draw'] );
		$total = intval( 2 );
		$filter = intval( 2 );
		$data = array ( array ( 'tiger','nixon','system','edin','25','320'), array ( 'tiger','cixon','system','edin','25','320'), array ( 'tiger','sixon','system','edin','25','320'));
		// echo json_encode($data);exit;
		$echo = array (
			'draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $filter, 'data' => $data
			);
		header('Content-Type: application/json');
		echo json_encode($echo);
	}

	public function testjoss() {
		$joss = $this->controller('joss');
		echo $joss;
	}

}

/* End of file welcome.php */
/* Location: ./application/modules/welcome/controllers/welcome.php */