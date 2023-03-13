<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$data['allacc'] = $this->Crud->read_single('user_id', $user_id, 'ka_account');
		
		$data['title'] = 'Accounts | '.app_name;
		$data['page_active'] = 'account';
		
		$this->load->view('designs/header', $data);
		$this->load->view('account', $data);
		$this->load->view('designs/footer', $data);
	}
}
