<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		// check if user is from bot
		$ref_bot = '';
		$ref_bot = $this->input->get('ref');
		$ref_bot_push = $this->input->get('push');
		$ref_bot_sender = $this->input->get('sender');
		$ref_acc_link_token = $this->input->get('account_linking_token');
		$ref_redirect_url = $this->input->get('redirect_uri');
		if($ref_bot != '' && $ref_bot_push != '' && $ref_bot_sender != '' && $ref_acc_link_token != '' && $ref_redirect_url != ''){
			$ref_bot_data = array('ka_ref' => $ref_bot, 'ka_ref_push' => $ref_bot_push, 'ka_ref_sender' => $ref_bot_sender, 'ka_ref_token' => $ref_acc_link_token, 'ka_ref_redirect' => $ref_redirect_url);
			$this->session->set_userdata($ref_bot_data);
		}
		// end bot referral
	
/////////////////////////////////////////// FB LOGIN START //////////////////////////////////////////////		
		////// check if it's facebook graph api login ///////////
		if($this->session->userdata('ka_ref_sender') != ''){
			$ref_bot_sender = $this->session->userdata('ka_ref_sender');
			
			$fb_app_id = '398553420496868';
			$fb_app_secret = '5847c97280e8cd47a881d72917491a04';
			$fb_ref_code = $this->input->get('code');
			if($fb_ref_code) {		
				// parsing the result to getting access token.
				$fb_get_token = json_decode($this->Crud->fb_login("https://graph.facebook.com/oauth/access_token?client_id=".$fb_app_id."&redirect_uri=".urlencode(base_url('login'))."&client_secret=".$fb_app_secret."&code=".urlencode($fb_ref_code)), true);
			  redirect(base_url('login?access_token='.$fb_get_token['access_token']), 'refresh');
			}
			
			$fb_access_token = $this->input->get('access_token');
			if($fb_access_token) {
			  // getting all user info using access token.
			  $fbuser_info = json_decode($this->Crud->fb_login("https://graph.facebook.com/v2.6/me?fields=id,first_name,last_name,email,gender,locale,picture&access_token=".$fb_access_token), true);
			 if(!empty($fbuser_info)) {
				  $fbuser_id = $fbuser_info['id'];
				  $fbuser_first_name = $fbuser_info['first_name'];
				  $fbuser_last_name = $fbuser_info['last_name'];
				  $fbuser_email = $fbuser_info['email'];
				  $fbuser_gender = $fbuser_info['gender'];
				  
				  // check if user already exist in database
				  $check_fb = $this->Crud->read_single('fbbot_psid', $ref_bot_sender, 'ka_user');
				  if(count($check_fb) <= 0){
					  // register user
					  $fb_reg_date = date(fdate);
					  $fb_pass = md5($reg_date);
					  
						//===get nicename and convert to seo friendly====
						$nicename = strtolower($fbuser_first_name);
						$nicename = preg_replace("/[^a-z0-9_\s-]/", "", $nicename);
						$nicename = preg_replace("/[\s-]+/", " ", $nicename);
						$nicename = preg_replace("/[\s_]/", "-", $nicename);
						//================================================
						
						$fb_username = $nicename.'-'.rand();
						
						$ins_data = array(
							'username' => $fb_username,
							'password' => $fb_pass,
							'othername' => $fbuser_first_name,
							'lastname' => $fbuser_last_name,
							'email' => $fbuser_email,
							'sex' => ucwords($fbuser_gender),
							'fbid' => $fbuser_id,
							'activation_code' => 'Facebook',
							'activate' => 1,
							'role' => 'User',
							'reg_date' => $fb_reg_date
						);
						
						$ins_id = $this->Crud->create('ka_user', $ins_data);
						if($ins_id > 0) {
							$data['reset'] = TRUE;
							
							// send activation email
							$email_result = '';
							$from = app_email;
							$subject = 'Registration Notification';
							$name = app_name;
							$sub_head = 'Registration From Facebook';
							
							$body = '
								<div class="mname">Dear '.ucwords($fbuser_first_name).',</div><br />
								Your account was created on '.app_name.' through Facebook.<br />
								You can now start your automated savings. Thank you.<br /><br />
								Warm Regards
							';
							
							$email_result = $this->Crud->send_email($fbuser_email, $from, $subject, $body, $name, $sub_head);
							
							if($email_result == TRUE){
								// now admins
								$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
								$abody = '
									<div class="mname">Dear '.app_name.',</div><br />
									<b>'.ucwords($fbuser_first_name).' '.ucwords($fbuser_last_name).'</b> just registered on the Website through Facebook.<br /><br />
									Warm Regards
								';
								$this->Crud->send_email($admin_list, $from, $subject, $abody, $name, $sub_head);
							}
							
							// requery data to redirect
							$check_fb = $this->Crud->read_single('id', $ins_id, 'ka_user');
						}
				  } else {
					  $upd = array('fbid' => $fbuser_id, 'email' => $fbuser_email);
					  $this->Crud->update('fbbot_psid', $ref_bot_sender,'ka_user', $upd); 
				  }
				  
				  // keep user session here
				  if(!empty($check_fb)) {
					  foreach($check_fb as $row) {
						  //update status
						  $first_log = $row->last_log; //to check first time user
						  $now = date("Y-m-d H:i:s");
						  $status_update = array('status'=>1, 'last_log'=>$now);
						  $this->Crud->update('id', $row->id, 'ka_user', $status_update);
							
							//get logo
							$logo_path = 'assets/images/users/avatar300.png';
							$getimg = $this->Crud->read_single('id', $row->pics, 'ka_img');
							if(!empty($getimg)){
								foreach($getimg as $img){
									$logo_path = $img->pics_square;	
								}
							}
							
							//get state name
							$state_name = '';
							$getstate = $this->Crud->read_single('id', $row->state, 'ka_state');
							if(!empty($getstate)){
								foreach($getstate as $state){
									$state_name = $state->name;	
								}
							}
							
							//get country details
							$country_name = '';
							$country_code = '';
							$country_currency = '';
							$getcountry = $this->Crud->read_single('id', $row->country, 'ka_country');
							if(!empty($getcountry)){
								foreach($getcountry as $country){
									$country_name = $country->name;
									$country_code = $country->code;
									$country_currency = $country->currency;	
								}
							}
							
							//add data to session
							$s_data = array (
								'kas_id' => $row->id,
								'kas_username' => $row->username,
								'kas_user_email' => $row->email,
								'kas_user_lastlog' => $row->last_log,
								'kas_user_status' => $row->status,
								'kas_user_othername' => $row->othername,
								'kas_user_lastname' => $row->lastname,
								'kas_user_dob' => $row->dob,
								'kas_user_sex' => $row->sex,
								'kas_user_phone' => $row->phone,
								'kas_user_address' => $row->address,
								'kas_user_state_id' => $row->state,
								'kas_user_state' => $state_name,
								'kas_user_country_id' => $row->country,
								'kas_user_country_name' => $country_name,
								'kas_user_country_code' => $country_code,
								'kas_user_country_currency' => $country_currency,
								'kas_user_marital' => $row->marital,
								'kas_user_pics' => $logo_path,
								'kas_user_bio' => $row->bio,
								'kas_user_role' => $row->role,
								'kas_user_activation_code' => $row->activation_code,
								'kas_user_activate' => $row->activate,
								'kas_user_reg_date' => $row->reg_date,
								'ka_ref_sender' => $ref_bot_sender,
								'logged_in' => TRUE
							);
							$this->session->set_userdata($s_data);
							
							///////// START FB BOT REDIRECT //////////
							// redirect to bot, if coming from bot
							if($this->session->userdata('ka_ref') != ''){
								// save bot token in user profile
								$bot_save_data = array('fbbot_tid' => $this->session->userdata('ka_ref_sender'), 'fbbot_token' => $this->session->userdata('ka_ref_token'));
								$this->Crud->update('id', $row->id, 'ka_user', $bot_save_data);
								
								$ref_auth_code = $this->session->userdata('ka_ref_push');
								$ref_redirect_url = $this->session->userdata('ka_ref_redirect').'&authorization_code='.$ref_auth_code;
								// distory bot session before redirecting
								$bot_newdata = array('ka_ref' => '', 'ka_ref_push' => '', 'ka_ref_sender' => '', 'ka_ref_token' => '', 'ka_ref_redirect' => '');
								$this->session->unset_userdata($bot_newdata);
								$this->session->sess_destroy();
								redirect($ref_redirect_url, 'refresh');
							}
							///////// END FB BOT REDIRECT //////////
							
							$redir = $this->session->userdata('kas_redirect');
							// redirect page
							if($redir==''){$redir = 'dashboard/';}
							redirect(base_url($redir), 'refresh');
					  }
				  }
			  }
		   } else {
				$this->session->set_flashdata('message', 'Error while facebook user information.');
				//redirect(base_url('login'), 'refresh');
		   }
		   $data['authUrl'] = 'https://graph.facebook.com/oauth/authorize?client_id='.$fb_app_id.'&redirect_uri='.base_url('login').'&scope=email';
		} else {$data['authUrl'] = '';}
		///////// end facebook graph api login ////////////////
/////////////////////////////////////////// FB LOGIN ENDS //////////////////////////////////////////////
		
		// redirect if coming from native login
		$redir = $this->session->userdata('kas_redirect');
		if($this->session->userdata('logged_in') == TRUE){
			if($redir==''){$redir = 'dashboard/';}
			redirect(base_url($redir), 'refresh');	
		} 
		
		$data['reset'] = FALSE;
		
		if($_POST) {
			$email = $_POST['email'];
			$password = $_POST['password'];
			$password = md5($password);
			if(isset($_POST['remember-me'])){$remind='true';}else{$remind='';}
			
			if($this->Crud->check2('email', $email, 'password', $password, 'ka_user') <= 0){
				$data['err_msg'] = $this->Crud->msg('danger', 'Username or password is wrong!');
			} else if($this->Crud->check3('email', $email, 'password', $password, 'activate', 0, 'ka_user') > 0){
				$data['err_msg'] = $this->Crud->msg('warning', 'Kindly verify your account');
			} else {
				$query = $this->Crud->read2('email', $email, 'password', $password, 'ka_user');
				if(!empty($query)) {
					foreach($query as $row) {
						//update status
						$first_log = $row->last_log; //to check first time user
						
						$now = date("Y-m-d H:i:s");
						$status_update = array('status'=>1, 'last_log'=>$now);
						$this->Crud->update('id', $row->id, 'ka_user', $status_update);
						
						///////// START FB BOT REDIRECT //////////
						// redirect to bot, if coming from bot
						if($this->session->userdata('ka_ref') != ''){
							// save bot token in user profile
							$bot_save_data = array('fbbot_tid' => $this->session->userdata('ka_ref_sender'), 'fbbot_token' => $this->session->userdata('ka_ref_token'));
							$this->Crud->update('id', $row->id, 'ka_user', $bot_save_data);
							
							$ref_auth_code = $this->session->userdata('ka_ref_push');
							$ref_redirect_url = $this->session->userdata('ka_ref_redirect').'&authorization_code='.$ref_auth_code;
							// distory bot session before redirecting
							$bot_newdata = array('ka_ref' => '', 'ka_ref_push' => '', 'ka_ref_sender' => '', 'ka_ref_token' => '', 'ka_ref_redirect' => '');
							$this->session->unset_userdata($bot_newdata);
							$this->session->sess_destroy();
							redirect($ref_redirect_url, 'refresh');
						}
						///////// END FB BOT REDIRECT //////////
						
						//get logo
						$logo_path = 'assets/images/users/avatar300.png';
						$getimg = $this->Crud->read_single('id', $row->pics, 'ka_img');
						if(!empty($getimg)){
							foreach($getimg as $img){
								$logo_path = $img->pics_square;	
							}
						}
						
						//get state name
						$state_name = '';
						$getstate = $this->Crud->read_single('id', $row->state, 'ka_state');
						if(!empty($getstate)){
							foreach($getstate as $state){
								$state_name = $state->name;	
							}
						}
						
						//get country details
						$country_name = '';
						$country_code = '';
						$country_currency = '';
						$getcountry = $this->Crud->read_single('id', $row->country, 'ka_country');
						if(!empty($getcountry)){
							foreach($getcountry as $country){
								$country_name = $country->name;
								$country_code = $country->code;
								$country_currency = $country->currency;	
							}
						}
						
						//add data to session
						$s_data = array (
							'kas_id' => $row->id,
							'kas_username' => $row->username,
							'kas_user_email' => $row->email,
							'kas_user_lastlog' => $row->last_log,
							'kas_user_status' => $row->status,
							'kas_user_othername' => $row->othername,
							'kas_user_lastname' => $row->lastname,
							'kas_user_dob' => $row->dob,
							'kas_user_sex' => $row->sex,
							'kas_user_phone' => $row->phone,
							'kas_user_address' => $row->address,
							'kas_user_state_id' => $row->state,
							'kas_user_state' => $state_name,
							'kas_user_country_id' => $row->country,
							'kas_user_country_name' => $country_name,
							'kas_user_country_code' => $country_code,
							'kas_user_country_currency' => $country_currency,
							'kas_user_marital' => $row->marital,
							'kas_user_pics' => $logo_path,
							'kas_user_bio' => $row->bio,
							'kas_user_role' => $row->role,
							'kas_user_activation_code' => $row->activation_code,
							'kas_user_activate' => $row->activate,
							'kas_user_reg_date' => $row->reg_date,
							'logged_in' => TRUE
						);
					}
					
					$check = $this->session->set_userdata($s_data);
					
					// redirect page
					if($redir==''){$redir = 'dashboard/';}
					redirect(base_url($redir), 'refresh');
				}
			}
		}
		
		$data['title'] = 'Login | '.app_name;
		$data['page_active'] = 'login';
		
		$this->load->view('login', $data);
	}
}
