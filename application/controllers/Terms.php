<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Terms extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		$data['title'] = app_name.' | Terms of Services';
		$data['page_active'] = 'terms';
		
		$this->load->view('designs/main_header', $data);
		$this->load->view('terms', $data);
		$this->load->view('designs/main_footer', $data);
	}
}
