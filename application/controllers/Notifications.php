<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {

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
		
		$data['allnotify'] = $this->Crud->read_single('user_id', $user_id, 'ka_notify');
		
		$data['title'] = 'Notifications | '.app_name;
		$data['page_active'] = 'notification';
		
		$this->load->view('designs/header', $data);
		$this->load->view('notification', $data);
		$this->load->view('designs/footer', $data);
	}
	
	public function v($param1='') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$push_notify = '';
		
		if($param1 == ''){
			redirect(base_url('notifications'), 'refresh');
		} else {
			// read notification
			$getnoti = $this->Crud->read_single('nhash', $param1, 'ka_notify');
			if(empty($getnoti)){
				redirect(base_url('notifications'), 'refresh');
			} else {
				foreach($getnoti as $noti){
					$id = $noti->id;
					$item_id = $noti->item_id;
					$item = $noti->item;
					
					if($item == 'personal'){
						$push_notify = 'savings/personal/v/'.$item_id;
					} else if($item == 'vault' || $item == 'voluntary'){
						$push_notify = 'vaults';
					} else if($item == 'offer'){
						$push_notify = 'offer';
					}
				}
				
				// now make nofication read
				$noti_data = array('new'=>0);
				$this->Crud->update('id', $id, 'ka_notify', $noti_data);
			}
			
			// redirect to push notification
			if($push_notify == ''){
				redirect(base_url('notifications'), 'refresh');
			} else {
				redirect(base_url($push_notify), 'refresh');
			}
		}
	}
	
	public function clear() {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$clearall = $this->Crud->read_single('user_id', $user_id, 'ka_notify');
		if(!empty($clearall)){
			foreach($clearall as $all){
				if($all->new == 1){
					$new_data = array('new'=>0);
					$this->Crud->update('id', $all->id, 'ka_notify', $new_data);
				}
			}
		}
		
		$data['allnotify'] = $this->Crud->read_single('user_id', $user_id, 'ka_notify');
		
		$data['title'] = 'Notifications | '.app_name;
		$data['page_active'] = 'notification';
		
		$this->load->view('designs/header', $data);
		$this->load->view('notification', $data);
		$this->load->view('designs/footer', $data);
	}
}
