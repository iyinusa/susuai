<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct() {
        parent::__construct();
		$this->load->model('Crud_messenger');
    }
	
	public function index() {
		if($this->session->userdata('logged_in') == TRUE){
			redirect(base_url('dashboard'), 'refresh');	
		} 
		
		$data['reset'] = FALSE;
		
		if($_POST) {
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$phone = $_POST['phone'];
			$password = $_POST['password'];
			$confirm = $_POST['confirm'];
			$reg_date = date(fdate);
			
			$opt_id = $_POST['opt_id']; // check if user checked messenger
			$opt_in = $_POST['opt_in']; // get unique opt id for conversation on messenger
			
			if($this->Crud->check('email', $email, 'ka_user') > 0){
				$data['err_msg'] = $this->Crud->msg('danger', 'Email address already attached to an account');	
			} else {
				if($password != $confirm){
					$data['err_msg'] = $this->Crud->msg('info', 'Password not matched');
				} else {
					//===get nicename and convert to seo friendly====
					$nicename = strtolower($firstname);
					$nicename = preg_replace("/[^a-z0-9_\s-]/", "", $nicename);
					$nicename = preg_replace("/[\s-]+/", " ", $nicename);
					$nicename = preg_replace("/[\s_]/", "-", $nicename);
					//================================================
					
					$password = md5($password);
					$username = $nicename.'-'.rand();
					$activation_code = time().rand();
					
					$ins_data = array(
						'username' => $username,
						'password' => $password,
						'othername' => $firstname,
						'lastname' => $lastname,
						'email' => $email,
						'phone' => $phone,
						'activation_code' => $activation_code,
						'fbbot_opt_id' => $opt_id,
						'fbbot_opt_in' => $opt_in,
						'fbbot_not_sync' => 1,
						'role' => 'User',
						'reg_date' => $reg_date
					);
					
					$ins_id = $this->Crud->create('ka_user', $ins_data);
					if($ins_id > 0) {
						$data['err_msg'] = $this->Crud->msg('success', 'Account created, please check '.$email.' INBOX or SPAM to activate your account');
						$data['reset'] = TRUE;
						
						// send activation email
						$email_result = '';
						$from = app_email;
						$subject = 'Account Activation';
						$name = app_name;
						$sub_head = 'Activate Email Address';
						
						$body = '
							<div class="mname">Dear '.ucwords($firstname).',</div><br />
							Your account was created on '.app_name.', kindly click below link to activate your account.<br />
							<div class="mbtn"><a href="'.base_url('register/activate/'.$activation_code.'/'.$username).'" class="btn btn-primary">Activate Now</a></div>In case button do not work, kindly copy and paste below link to browser.<br />'.base_url('register/activate/'.$activation_code.'/'.$username).'<br /><br />
							Warm Regards
						';
						
						$email_result = $this->Crud->send_email($email, $from, $subject, $body, $name, $sub_head);
						
						if($email_result == TRUE){
							// now admins
							$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
							$abody = '
								<div class="mname">Dear '.app_name.',</div><br />
								<b>'.ucwords($firstname).' '.ucwords($lastname).'</b> just registered on the Website<br /><br />
								Warm Regards
							';
							$this->Crud->send_email($admin_list, $from, $subject, $abody, $name, $sub_head);
						}
						
						// check and send notification on bot
						if($opt_in == 'checked') {
							$accessToken = 'EAAFqe3MNfZBQBAGXZBxzYiTQkHO1o55RjcqcZCeg85FaOdQfq0NJfF6ndWEadoy0piTC7x5f8P6XR9GEPhmo0yDiBnC68qajqbnZCTqWkBQSxGC1aZCb4wJQwfTJeJihT1qIVUMjivyYpOd0BOPZBmcoImJ9iWfdE0mcnuVlkffgZDZD';
							$answer = array(
								'text' => "Hi ".ucwords($firstname)."! You registered from the SusuAI Website, Click 'Sync and Use' to be able to manage your account from Messenger",  
								'quick_replies' => array(
									array(
										'content_type' => 'text',
										'title' => 'Sync and Use',
										'payload' => 'uoptin | '.$opt_id,
										'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
									)
								)
							);
							$response = array(
								'recipient' => array('user_ref' => $opt_id),
								'message' => $answer
							);
							$this->Crud_messenger->message($accessToken, $response);
						}
					} else {
						$data['err_msg'] = $this->Crud->msg('danger', 'Something went wrong, please try again');
					}
				}
			}
			
		}
		
		$data['title'] = 'Register | '.app_name;
		$data['page_active'] = 'register';
		
		$this->load->view('register', $data);
	}
	
	public function activate($param1='', $param2=''){
		if($param1 != '' && $param2 != ''){
			$check = $this->Crud->read2('activation_code', $param1, 'username', $param2, 'ka_user');
			if(!empty($check)){
				foreach($check as $ch){
					$id = $ch->id;
					$activate = $ch->activate;	
					if($activate == 1){
						$data['err_msg'] = $this->Crud->msg('success', 'Already activated, try and Login or use Forget Password');	
					} else {
						// update record
						$upd_data = array('activate'=>1);
						if($this->Crud->update('id', $id, 'ka_user', $upd_data) > 0){
							$data['err_msg'] = $this->Crud->msg('success', 'Account Activated! You can now Sign In');
						} else {
							$data['err_msg'] = $this->Crud->msg('warning', 'Could not activate you this time');	
						}
					}
				}
			} else {
				$data['err_msg'] = $this->Crud->msg('warning', 'Record not found');	
			}
		} else {redirect(base_url('register'), 'refresh');}
		
		$data['title'] = 'Account Activation | '.app_name;
		$data['page_active'] = 'activate';
		
		$this->load->view('activate', $data);
	}
}
