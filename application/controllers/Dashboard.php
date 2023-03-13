<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	private $country_name;
	private $country_code;
	private $country_curr;
	function __construct() {
        parent::__construct();
		$this->country_name = $this->Crud->country_data($this->session->userdata('kas_user_country_id'), 'name');
		$this->country_code = $this->Crud->country_data($this->session->userdata('kas_user_country_id'), 'code');
		$this->country_curr = $this->Crud->country_data($this->session->userdata('kas_user_country_id'), 'currency');
    }
	
	public function index() {
		$data['my_curr'] = $this->country_curr; // pass to views
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$data['allpersonal'] = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
		
		$data['title'] = 'Dashboard | '.app_name;
		$data['page_active'] = 'dashboard';
		
		$this->load->view('designs/header', $data);
		$this->load->view('dashboard', $data);
		$this->load->view('designs/footer', $data);
	}
}
