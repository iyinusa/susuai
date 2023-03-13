<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Offers extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		redirect(base_url('offers/jumia'), 'refresh');
	}
	
	/////////////// ********** JUMIA OFFER ************ ///////////////////
	public function jumia() {
		$data['title'] = app_name.' | Jumia Offer';
		$data['page_active'] = 'offers';
		
		$this->load->view('designs/main_header', $data);
		$this->load->view('offers/jumia', $data);
		$this->load->view('designs/main_footer', $data);
	}

}
