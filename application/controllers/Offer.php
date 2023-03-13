<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Offer extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		redirect(base_url('offer/lists'), 'refresh');
	}
	
	/////////////// ********** MANAGE OFFERS ************ ///////////////////
	public function lists($param1 = '', $param2 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$user_lastname = $this->session->userdata('kas_user_lastname');
			$user_othername = $this->session->userdata('kas_user_othername');
			$user_email = $this->session->userdata('kas_user_email');
			$user_phone = $this->session->userdata('kas_user_phone');
			$user_country_id = $this->session->userdata('kas_user_country_id');
			$user_country = $this->session->userdata('kas_user_country_name');
			$data['my_curr'] = $this->Crud->country_data($user_country_id, 'currency');
		}
		$now = date(fdate);
		
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		
		if($param1 == 'a'){
			if($_POST){
				$com_id = $_POST['com_id'];
				$saving_id = $_POST['saving_id'];
				$offer_no = rand();
				$product_link = $_POST['product_link'];
				$status = 'Pending';
				
				if($user_country == 'Nigeria') {
					$offer_link = 'http://c.jumia.io/?a=31860&c=11&p=r&E=kkYNyk2M4sk%3d&ckmrdr='.$product_link.'&s1='.$offer_no.'&utm_source=cake&utm_medium=affiliation&utm_campaign=31860&utm_term='.$offer_no;
				}
				
				// get savings
				$target = '';
				$getsave = $this->Crud->read_single('id', $saving_id, 'ka_personal');
				if(!empty($getsave)){
					foreach($getsave as $save){
						$target = $save->target;
					}
				}
				
				// get commission
				$comm = '';
				$getcomm = $this->Crud->read_single('id', $com_id, 'ka_offer_commission');
				if(!empty($getcomm)){
					foreach($getcomm as $com){
						$comm = $com->com;
					}
				}
				
				$interest = $target * ($comm / 100);
				
				if($this->Crud->check2('user_id', $user_id, 'saving_id', $saving_id, 'ka_offer') > 0){
					$data['err_msg'] = $this->Crud->msg('danger', 'You already subscribe this savings to an offer');
				} else {
					$ins_data = array(
						'user_id' => $user_id, 
						'com_id' => $com_id, 
						'saving_id' => $saving_id, 
						'interest' => $interest, 
						'offer_no' => $offer_no, 
						'product_link' => $product_link, 
						'offer_link' => $offer_link, 
						'status' => $status, 
						'reg_date' => $now
					);
					$ins_id = $this->Crud->create('ka_offer', $ins_data);
				
					if($ins_id > 0){
						$data['err_msg'] = $this->Crud->msg('success', 'Offer Created and Linked to Savings');	
					} else {
						$data['err_msg'] = $this->Crud->msg('warning', 'Please try later');
					}
				}
			}
		} else if($param1 == 'd'){
			if($param2 != '') {
				$getoffer = $this->Crud->read2('id', $param2, 'user_id', $user_id, 'ka_offer');
				if(!empty($getoffer)){
					foreach($getoffer as $offer){
						$data['d_id'] = $offer->id;
						//$data['d_name'] = $offer->name;
					}
				}
			}
			
			if($_POST){
				$d_offer_id = $_POST['d_offer_id'];
				if(isset($_POST['btnYes'])){
					$this->Crud->delete('id', $d_offer_id, 'ka_offer');
				}
				redirect(base_url('offer/lists'), 'refresh');
			}
		}
		
		$data['allpartner'] = $this->Crud->read_single('country_id', $user_country_id, 'ka_offer_partner');
		$data['alloffer'] = $this->Crud->read_single('user_id', $user_id, 'ka_offer');
		$data['allpersonal'] = $this->Crud->read2('user_id', $user_id, 'complete', 0, 'ka_personal');
		
		$data['title'] = 'Offers | '.app_name;
		$data['page_active'] = 'offer';
		
		$this->load->view('designs/header', $data);
		$this->load->view('offer', $data);
		$this->load->view('designs/footer', $data);
	}
	
	public function get_offer() {
		$com_list = '';
		if($_POST) {
			$partner = $_POST['partner_id'];	
			$allcom = $this->Crud->read_single('pertner_id', $partner, 'ka_offer_commission');
			if(!empty($allcom)) {
				foreach($allcom as $com) {
					$com_list .= '<option value="'.$com->id.'">'.$com->name.'</option>';	
				}
			}
		}
		
		echo $com_list;
	}

}
