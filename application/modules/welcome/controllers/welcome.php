<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Public_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->enqueue_style = array (
			'style' => array ( 'style.css', array(), '1.0.0' )
			);

		$this->enqueue_script = array (
			'meneh' => array ( 'meneh.js',array(),'3.2.0', TRUE )
			);

		$this->content_theme = 'welcome_message';
		return $this->render_theme();
	}

	public function testdb() {
		$this->load->database();
		echo_r($this->db->list_tables());
	}

	public function testapi() {
		// $this->load->config('rest_api');
		// echo_r(config_item('api_logins'));
		$value['secret'] = '40acf3555ec968ecab9542ffeaf7bc281bb4af0f';
		echo get_remote ( 'example/users', $value );//
	}

	public function request() {
		$url = 'http://codeigniter.dev/hmvci/rahendz/index.php/api/key/index';
		echo put_remote($url,array('keys'=>'masbottt'));
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

}

/* End of file welcome.php */
/* Location: ./application/modules/welcome/controllers/welcome.php */