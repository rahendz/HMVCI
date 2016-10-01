<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Public_Controller {

	public function __construct() {
		parent::__construct();
		$this->theme_var['config']['frontend'] = 'default';
	}

	public function index() {
		$this->enqueue_style(array(
			'style' => array ( 'style.css', array(), '1.0.0' )
			));

		$this->enqueue_script (array (
			'meneh' => array ( 'meneh.js',array(),'3.2.0', TRUE )
			));

		$this->theme_var['content'] = 'welcome_message';
		return $this->render_theme();
	}

	public function testdb() {
		$welcome = new Model;//
		// $welcome->where = array ('id'=>'1');
		$welcome->table = 'balita_master';
		echo_r($welcome->get_one());
		$welcome->table = 'users';
		echo_r($welcome->get_all());
		$welcome2 = $this->model('m_welcome');
		echo_r($welcome2->test());
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
			'style' => array ( 'style.css', array(), '1.0.0' ),
			'datatable' => array ( 'css/jquery.dataTables.min.css', array(), '1.10.9' )
			));
		// $this->enqueue_style('style','style.css',array(),'1.0.0');

		$this->enqueue_script ( array (
					'datatable' => array ( 'js/jquery.dataTables.js', array ('jquery'), '1.10.9', true ),
					'trigger' => array ( 'trigger.js',array(),'1.0.0', true )
					));

		$this->content_theme = 'welcome_datatable';
		return $this->render_theme();
	}

	public function data_record() {
		$draw = intval( $_REQUEST['draw'] );
		$total = intval( 2 );
		$filter = intval( 2 );
		// $data = json_encode('[["Tiger","Nixon","System Architect","Edinburgh","25th Apr 11","$320,800"],["Garrett","Winters","Accountant","Tokyo","25th Jul 11","$170,750"],["Ashton","Cox","Junior Technical Author","San Francisco","12th Jan 09","$86,000"],["Cedric","Kelly","Senior Javascript Developer","Edinburgh","29th Mar 12","$433,060"],["Airi","Satou","Accountant","Tokyo","28th Nov 08","$162,700"],["Brielle","Williamson","Integration Specialist","New York","2nd Dec 12","$372,000"],["Herrod","Chandler","Sales Assistant","San Francisco","6th Aug 12","$137,500"],["Rhona","Davidson","Integration Specialist","Tokyo","14th Oct 10","$327,900"],["Colleen","Hurst","Javascript Developer","San Francisco","15th Sep 09","$205,500"],["Sonya","Frost","Software Engineer","Edinburgh","13th Dec 08","$103,600"],["Jena","Gaines","Office Manager","London","19th Dec 08","$90,560"],["Quinn","Flynn","Support Lead","Edinburgh","3rd Mar 13","$342,000"],["Charde","Marshall","Regional Director","San Francisco","16th Oct 08","$470,600"],["Haley","Kennedy","Senior Marketing Designer","London","18th Dec 12","$313,500"],["Tatyana","Fitzpatrick","Regional Director","London","17th Mar 10","$385,750"],["Michael","Silva","Marketing Designer","London","27th Nov 12","$198,500"],["Paul","Byrd","Chief Financial Officer (CFO)","New York","9th Jun 10","$725,000"],["Gloria","Little","Systems Administrator","New York","10th Apr 09","$237,500"],["Bradley","Greer","Software Engineer","London","13th Oct 12","$132,000"],["Dai","Rios","Personnel Lead","Edinburgh","26th Sep 12","$217,500"],["Jenette","Caldwell","Development Lead","New York","3rd Sep 11","$345,000"],["Yuri","Berry","Chief Marketing Officer (CMO)","New York","25th Jun 09","$675,000"],["Caesar","Vance","Pre-Sales Support","New York","12th Dec 11","$106,450"],["Doris","Wilder","Sales Assistant","Sidney","20th Sep 10","$85,600"],["Angelica","Ramos","Chief Executive Officer (CEO)","London","9th Oct 09","$1,200,000"],["Gavin","Joyce","Developer","Edinburgh","22nd Dec 10","$92,575"],["Jennifer","Chang","Regional Director","Singapore","14th Nov 10","$357,650"],["Brenden","Wagner","Software Engineer","San Francisco","7th Jun 11","$206,850"],["Fiona","Green","Chief Operating Officer (COO)","San Francisco","11th Mar 10","$850,000"],["Shou","Itou","Regional Marketing","Tokyo","14th Aug 11","$163,000"],["Michelle","House","Integration Specialist","Sidney","2nd Jun 11","$95,400"],["Suki","Burks","Developer","London","22nd Oct 09","$114,500"],["Prescott","Bartlett","Technical Author","London","7th May 11","$145,000"],["Gavin","Cortez","Team Leader","San Francisco","26th Oct 08","$235,500"],["Martena","Mccray","Post-Sales support","Edinburgh","9th Mar 11","$324,050"],["Unity","Butler","Marketing Designer","San Francisco","9th Dec 09","$85,675"],["Howard","Hatfield","Office Manager","San Francisco","16th Dec 08","$164,500"],["Hope","Fuentes","Secretary","San Francisco","12th Feb 10","$109,850"],["Vivian","Harrell","Financial Controller","San Francisco","14th Feb 09","$452,500"],["Timothy","Mooney","Office Manager","London","11th Dec 08","$136,200"],["Jackson","Bradshaw","Director","New York","26th Sep 08","$645,750"],["Olivia","Liang","Support Engineer","Singapore","3rd Feb 11","$234,500"],["Bruno","Nash","Software Engineer","London","3rd May 11","$163,500"],["Sakura","Yamamoto","Support Engineer","Tokyo","19th Aug 09","$139,575"],["Thor","Walton","Developer","New York","11th Aug 13","$98,540"],["Finn","Camacho","Support Engineer","San Francisco","7th Jul 09","$87,500"],["Serge","Baldwin","Data Coordinator","Singapore","9th Apr 12","$138,575"],["Zenaida","Frank","Software Engineer","New York","4th Jan 10","$125,250"],["Zorita","Serrano","Software Engineer","San Francisco","1st Jun 12","$115,000"],["Jennifer","Acosta","Junior Javascript Developer","Edinburgh","1st Feb 13","$75,650"],["Cara","Stevens","Sales Assistant","New York","6th Dec 11","$145,600"],["Hermione","Butler","Regional Director","London","21st Mar 11","$356,250"],["Lael","Greer","Systems Administrator","London","27th Feb 09","$103,500"],["Jonas","Alexander","Developer","San Francisco","14th Jul 10","$86,500"],["Shad","Decker","Regional Director","Edinburgh","13th Nov 08","$183,000"],["Michael","Bruce","Javascript Developer","Singapore","27th Jun 11","$183,000"],["Donna","Snider","Customer Support","New York","25th Jan 11","$112,000"]]');
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