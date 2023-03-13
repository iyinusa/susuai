<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		//if($this->session->userdata('logged_in') == FALSE){
//			redirect(base_url('login'), 'refresh');	
//		} else {
//			$kas_user_role = $this->session->userdata('kas_user_role');
//			$permit = array('User');
//			if(!in_array($kas_user_role, $permit)){
//				redirect(base_url(''), 'refresh');	
//			}
//		}
		
		$data['title'] = 'Welcome to '.app_name;
		$data['page_active'] = 'main';
		
		$this->load->view('designs/main_header', $data);
		$this->load->view('main', $data);
		$this->load->view('designs/main_footer', $data);
	}
}
