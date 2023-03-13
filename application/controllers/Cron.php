<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

	function __construct() {
        parent::__construct();
		$this->load->model('Crud_messenger');
    }
	
	public function index() {
		
	}
	
	////////////////// CONTRIBUTION REMINDER //////////////////////////
	public function p_contribute_email() {
		// get all personal active savings
		$pactive = $this->Crud->read2('active', 1, 'expired', 0, 'ka_personal');
		if(!empty($pactive)){
			foreach($pactive as $pa){
				$pa_id = $pa->id;
				$pa_user_id = $pa->user_id;
				$pa_name = $pa->name;
				$pa_type = $pa->type;
				$pa_duration = $pa->duration;
				$pa_saving = $pa->saving;
				$pa_saving_start = $pa->saving_start;
				$pa_saving_next = strtotime(date('Y-m-d', strtotime($pa->saving_next)));
				$pa_saving_current = strtotime(date('Y-m-d', strtotime($pa->saving_current)));
				$pa_cycle = $pa->cycle;
				
				$chk_cycle = $pa_cycle + 1;
				
				// check if contribution made already
				$pa_today = strtotime(date('Y-m-d'));
				$date_diff = $pa_today - $pa_saving_next;
				$date_diff = floor($date_diff / (60 * 60 * 24));
				
				// now only send if it's time for next contribution
				if($date_diff >= 0) {
					// get user details
					$pa_user_name = '';
					$pa_user_email = '';
					$pa_user_phone = '';
					$pa_fbbot_psid = '';
					$getuser = $this->Crud->read_single('id', $pa_user_id, 'ka_user');
					if(!empty($getuser)){
						foreach($getuser as $user){
							$pa_user_name = ucwords($user->othername);
							$pa_user_email = $user->email;
							$pa_user_phone = $user->phone;
							$pa_fbbot_psid = $user->fbbot_psid;
						}
					}
					
					// now save and send email notification
					$n_item_id = $pa_id;
					$n_hash = md5(time());
					$n_item = 'personal';
					$n_title = 'Personal Savings Reminder';
					$n_details = 'Your next contribution cycle ('.$chk_cycle.' of '.$pa_duration.') cycle of your '.ucwords($pa_name).' savings. <div class="mbtn"><a href="https://susu-ai.com/savings/personal/p/'.$pa_id.'">Click to Pay</a></div>';
					
					$this->Crud->notify($pa_user_id, $pa_user_name, $pa_user_email, $pa_user_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
				}
			}
		}
		
		// notify me when cron runs
		$this->Crud->send_email('iyinusa@yahoo.co.uk', app_email, 'Cron Runs', 'This is a successful runnings', 'SusuAI Cron', 'SusuAI Cron Job');
	}
	
	////////////////// INACTIVE REMINDER //////////////////////////
	public function p_inactive_email() {
		// get all personal active savings
		$pinactive = $this->Crud->read_single('active', 0, 'ka_personal');
		if(!empty($pinactive)){
			foreach($pinactive as $pa){
				$pa_id = $pa->id;
				$pa_user_id = $pa->user_id;
				$pa_name = $pa->name;
				$pa_type = $pa->type;
				$pa_duration = $pa->duration;
				$pa_saving_start = $pa->saving_start;
				$pa_cycle = $pa->cycle;
				
				if($pa_saving_start == '') {
					// get user details
					$pa_user_name = '';
					$pa_user_email = '';
					$pa_user_phone = '';
					$getuser = $this->Crud->read_single('id', $pa_user_id, 'ka_user');
					if(!empty($getuser)){
						foreach($getuser as $user){
							$pa_user_name = ucwords($user->othername);
							$pa_user_email = $user->email;
							$pa_user_phone = $user->phone;
						}
					}
					
					// now save and send email notification
					$n_item_id = $pa_id;
					$n_hash = md5(time());
					$n_item = 'personal';
					$n_title = 'Inactive Savings Reminder';
					$n_details = 'You are yet to start your '.ucwords($pa_name).' savings. Why not start your contribution today and let '.app_name.' automate it for you. <div class="mbtn"><a href="https://susu-ai.com/savings/personal/p/'.$pa_id.'">Start Savings</a></div>';
					
					$this->Crud->notify($pa_user_id, $pa_user_name, $pa_user_email, $pa_user_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
				}
			}
		}
	}
}
