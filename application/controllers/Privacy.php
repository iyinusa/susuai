<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Privacy extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		$data['title'] = app_name.' | Privacy Policy';
		$data['page_active'] = 'privacy';
		
		$this->load->view('designs/main_header', $data);
		$this->load->view('privacy', $data);
		$this->load->view('designs/main_footer', $data);
	}
}
