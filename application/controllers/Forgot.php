<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		$data['change'] = FALSE;
		
		if($_POST){
			$email = $_POST['email'];
				
			if($this->Crud->check('email', $email, 'ka_user') <= 0) {
				$data['err_msg'] = $this->Crud->msg('danger', 'Email not in database');
			} else {
				$time = time().rand();
				
				// get user details
				$othername = $email;
				$username = '';
				$getuser = $this->Crud->read_single('email', $email, 'ka_user');
				if(!empty($getuser)){
					foreach($getuser as $user){
						$username = $user->username;
						$othername = $user->othername;	
					}
				}
				
				if($username == ''){
					$data['err_msg'] = $this->Crud->msg('danger', 'Record not found');
				} else {
					$reg_data = array(
						'reset' => 1,
						'reset_stamp' => $time
					);
					if($this->Crud->update('email', $email, 'ka_user', $reg_data) > 0){
						$data['err_msg'] = $this->Crud->msg('success', 'Reset Instruction sent to '.$email);
						// send email
						$email_result = '';
						$from = app_email;
						$subject = 'Reset Password';
						$name = app_name;
						$sub_head = 'Reset Password Instruction';
						
						$body = '
							<div class="mname">Dear '.ucwords($othername).',</div><br/>
							You requested to reset password on '.app_name.', kindly click below link to set new password.
							<div class="mbtn"><a href="'.base_url('forgot/change/'.$time.'/'.$username).'" class="btn btn-primary">Reset Now</a></div>In case button do not work, kindly copy and paste below link to browser.<br />'.base_url('forgot/change/'.$time.'/'.$email).'<br /><br />
							Warm Regards.
						';
						
						$email_result = $this->Crud->send_email($email, $from, $subject, $body, $name, $sub_head);
					} else {
						$data['err_msg'] = $this->Crud->msg('danger', 'Please try later');
					}
				}
			}
		}
		
		$data['title'] = 'Forgot | '.app_name;
		$data['page_active'] = 'forgot';
		
		$this->load->view('forgot', $data);
	}
	
	public function change($param1='', $param2='') {
		$data['change'] = FALSE;
		$data['param1'] = '';
		$data['param2'] = '';
		
		// check record
		if($this->Crud->check2('reset_stamp', $param1, 'username', $param2, 'ka_user') <= 0){
			redirect(base_url('forgot'), 'refresh');
		} else {
			$getrec = $this->Crud->read2('reset_stamp', $param1, 'username', $param2, 'ka_user');
			if(!empty($getrec)){
				foreach($getrec as $rec){
					$id = $rec->id;
					$reset = $rec->reset;	
				}
			}
			
			if($reset == 0){
				redirect(bas_url('forgot'), 'refresh');
			} else {
				$data['change'] = TRUE;
				$data['param1'] = $param1;
				$data['param2'] = $param2;
				
				if($_POST){
					$new = $_POST['new'];
					$confirm = $_POST['confirm'];
					
					if($new != $confirm){
						$data['err_msg'] = $this->Crud->msg('warning', 'Password not matched');
					} else {
						$update_data = array(
							'password' => md5($new),
							'reset' => 0,
							'reset_stamp' => ''
						);
						
						if($this->Crud->update('username', $param2, 'ka_user', $update_data) > 0){
							$data['err_msg'] = $this->Crud->msg('success', 'Password Reset! You can <a href="'.base_url('login').'">Sign In</a>');
						} else {
							$data['err_msg'] = $this->Crud->msg('danger', 'Please try later');
						}
					}
				}
				
				$data['title'] = 'Forgot | '.app_name;
				$data['page_active'] = 'forgot';
				
				$this->load->view('forgot', $data);
			}
		}
	}
}
