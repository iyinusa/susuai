<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		$data['title'] = app_name.' | FAQ';
		$data['page_active'] = 'faq';
		
		$this->load->view('designs/main_header', $data);
		$this->load->view('faq', $data);
		$this->load->view('designs/main_footer', $data);
	}
}
