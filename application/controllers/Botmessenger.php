<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Botmessenger extends CI_Controller {
	
	private $accessToken;
	private $senderId;
	private $msgResp;
	private $answer;
	private $user_id;
	private $user_lastname;
	private $user_othername;
	private $user_email;
	private $bot_email;
	private $user_phone;
	private $user_status;
	private $user_currency;
	private $user_country;
	private $user_psid;
	function __construct() {
        parent::__construct();
		$this->load->model('Crud_messenger');
		$this->load->model('Crud');
		
		$this->accessToken = 'EAAFqe3MNfZBQBAN6IEpgqpkVjr6xJ632OA4idWEIRhzGI4mtgmpd05ptMhi1WTYtkslzOZBeTrSWsqqGmBFIog2d7ZBH40ajWcLvPFGIHfblawPlBYNWwYvzAuy5WLozLMJwaV14VbC04C01190J93m2JUgObovyb6ZBOn7BZC3LYrU2dwBqJ';
	}
	
	//////// BOT AI BLOCK ////////
	public function index() {
		/* validate verify token needed for setting up web hook */ 
		$hubVerifyToken = 'Susu_Msg_Bot#';
		if(isset($_REQUEST['hub_mode'])) {
			$challenge = $_REQUEST['hub_challenge'];
			$hub_verify_token = $_REQUEST['hub_verify_token'];
			if ($hub_verify_token === $hubVerifyToken) {
				//header("HTTP/1.1 200 OK");
				echo $challenge;
				die;
			}
		}
		
		// handle webhook post
		$input = json_decode(file_get_contents('php://input'), true);
		$this->bot_email = $input['entry'][0]['email'];
		 
		// webhook response iteration
		for($i = 0; $i < count($input['entry'][0]['messaging']); $i++) {
			$event = $input['entry'][0]['messaging'][$i];
			$senderId = $event['sender']['id'];
			$this->senderId = $senderId; // save in global variable
			
			// get text and quick reply message response
			if($event['message']){
				$textResp = $event['message']['text'];
				if($event['message']['quick_reply']){$textResp = $event['message']['quick_reply']['payload'];}
				// convert to lower string to uniform precised response
				$msgResp = strtolower($textResp);
				$this->_webhook($msgResp, $senderId);
				exit;
			} else if($event['postback']){ // postback response
				$textResp = $event['postback']['payload'];
				// convert to lower string to uniform precised response
				$msgResp = strtolower($textResp);
				$this->_webhook($msgResp, $senderId);
				exit;
			} else if($event['referral']){ // referral response
				$textResp = $event['referral']['ref'];
				// convert to lower string to uniform precised response
				$msgResp = strtolower($textResp);
				$this->_webhook($msgResp, $senderId);
				exit;
			} else if($event['account_linking']){ // account linking response
				$link_status = $event['account_linking']['status'];
				if($event['account_linking']['authorization_code']){
					// check if user have linked, and log user in
					$getuser = $this->Crud->read_single('fbbot_tid', $senderId, 'ka_user');
					if(!empty($getuser)){
						foreach($getuser as $guser){
							$link_user_id = $guser->id;
							$this->user_id = $guser->id;
							$this->user_othername = $guser->othername;
							$this->user_status = 1;
							$this->user_psid = $guser->fbbot_token;
						}
					}
					$upd_psid_data = array('status' => 1, 'fbbot_psid' => $senderId);
					$this->Crud->update('id', $link_user_id, 'ka_user', $upd_psid_data);
					
					// instant login notification
					$log_msg = array("text" => "Dear ".$this->user_othername.". You are now logged in to SusuAI. (y)");
					if($log_msg){$this->_send_message($senderId, $log_msg);}
					
					$textResp = $event['account_linking']['authorization_code'];
				} else {
					// logout user
					if($link_status == 'unlinked'){
						$this->user_status = 0;
						$upd_psid_data = array('status' => 0);
						$this->Crud->update('fbbot_psid', $senderId, 'ka_user', $upd_psid_data);
						
						// instant loggout notification
						$log_msg = array("text" => ";) You are now logged out of SusuAI. See you soon...meanwhile :/");
						if($log_msg){$this->_send_message($senderId, $log_msg);}
						
						$textResp = 'hi';
					}
				}
				
				// convert to lower string to uniform precised response
				$msgResp = strtolower($textResp);
				$this->_webhook($msgResp, $senderId);
				exit;
			}
		}
	}
	
	/////////// AI Webhook Block //////
	private function _webhook($msg='', $senderId='') {
		// default response to users
		$answer = array("text" => "Nice! To continue type 'hi' or 'hello', for assistance type 'help' or 'keywords'. :/");
		
		// check for opt-in from website
		if(strpos($msg, 'uoptin') !== false) {
			// break and get unique if referred from website
			$get_unique = explode(' | ', $msg);
			$get_unique_id = $get_unique[1];
			$upd_user = array('fbbot_tid' => $senderId, 'fbbot_psid' => $senderId); 
			$this->Crud->update('fbbot_opt_id', $get_unique_id, 'ka_user', $upd_user);
		}
		
		// get user session
		$user_status = 0;
		$fbbot_not_sync = 0; // use to track is user is not subscribed to bot from Website plugin
		$cache_saving_status = 0; // use this to track reccurent flag messages
		$user_saving_list = array(); // savings array list
		$user_acc_list = array(); // disburse account array list
		$getuser = $this->Crud->read_single('fbbot_tid', $senderId, 'ka_user');
		if(empty($getuser)){ // store user information
			$getprofile = $this->Crud_messenger->user_profile($this->accessToken, $senderId);
			$getprofile = json_decode($getprofile, true);
			if(!empty($getprofile)){
				$this->user_othername = $getprofile['first_name'];
				$this->user_lastname = $getprofile['last_name'];
				$pf_first_name = $getprofile['first_name'];
				$pf_last_name = $getprofile['last_name'];
				$pf_gender = $getprofile['gender'];
				$pf_reg_date = date('Y-m-d H:i:s');
				$pf_pass = md5(rand());
				
				//===get nicename and convert to seo friendly====
				$nicename = strtolower($pf_first_name);
				$nicename = preg_replace("/[^a-z0-9_\s-]/", "", $nicename);
				$nicename = preg_replace("/[\s-]+/", " ", $nicename);
				$nicename = preg_replace("/[\s_]/", "-", $nicename);
				//================================================
				
				$pf_username = $nicename.'-'.rand();
				
				// store in database
				$pf_ins_data = array(
					'username' => $pf_username,
					'password' => $pf_pass,
					'othername' => $pf_first_name,
					'lastname' => $pf_last_name,
					'email' => '',
					'sex' => ucwords($pf_gender),
					'activation_code' => 'Facebook',
					'fbbot_tid' => $senderId,
					'fbbot_psid' => $senderId,
					'activate' => 1,
					'role' => 'User',
					'reg_date' => $pf_reg_date
				);
				
				$user_id = $this->Crud->create('ka_user', $pf_ins_data);
				if($user_id > 0) {
					// send notification email to admin
					$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
					$from = app_email;
					$subject = 'New Bot User';
					$name = app_name;
					$sub_head = 'Registration From Messenger Bot';
					$abody = '
						<div class="mname">Dear '.app_name.',</div><br />
						<b>'.ucwords($pf_first_name).' '.ucwords($pf_last_name).'</b> just registered through Facebook Messenger Bot.<br /><br />
						Warm Regards
					';
					$this->Crud->send_email($admin_list, $from, $subject, $abody, $name, $sub_head);
				}
				$this->user_id = $user_id;
			}
		} else { // pull user information
			foreach($getuser as $guser){
				$user_id = $guser->id;
				$this->user_id = $guser->id;
				$this->user_lastname = $guser->lastname;
				$this->user_othername = $guser->othername;
				$this->user_email = $guser->email;
				$this->user_phone = $guser->phone;
				$user_country = $guser->country;
				$user_status = $guser->status;
				$user_last_log = $guser->last_log;
				$this->user_country = $user_country;
				$this->user_status = $guser->status;
				$this->user_psid = $guser->fbbot_psid;
				$fbbot_not_sync = $guser->fbbot_not_sync;
				
				// get currency
				$getct = $this->Crud->read_single('id', $user_country, 'ka_country');
				if(!empty($getct)){
					foreach($getct as $ct){
						$user_curr = $ct->currency;
						if($user_curr == 'N'){$user_curr = '₦';}
					}
				} else {$user_curr = '₦';}
				$this->user_currency = $user_curr;
				
				// try and store all user savings in array
				$getupers = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
				if(!empty($getupers)){
					foreach($getupers as $upers){
						$upers_name = trim($upers->name);
						$user_saving_list[] = strtolower($upers_name);
					}
				}
				
				// try and store all user disbursement accounts in array
				$getda = $this->Crud->read_single('user_id', $user_id, 'ka_account');
				if(!empty($getda)){
					foreach($getda as $da){
						$da_desc = trim($da->acc_desc);
						$user_acc_list[] = 'da '.strtolower($da_desc); // put da prefix to avoid conflicts of keywords
					}
				}
				
				// pull user savings cache if recurrent question is involve
				$cache_saving_status = 0;
				$getchs = $this->Crud->read_single('user_id', $user_id, 'ka_bot_p_cache');
				if(!empty($getchs)){
					foreach($getchs as $chs){
						$cache_saving_status = $chs->status;
						$chs_stage = $chs->stage;
						
						if($msg != 'start plan'){
							if($chs_stage >= 0){
								if($chs_stage == 0){
									$chs_save = array('name'=>ucwords($msg), 'stage'=>$chs_stage+1);
								} else if($chs_stage == 1){
									$chs_save = array('target'=>$msg, 'stage'=>$chs_stage+1);
								} else if($chs_stage == 2){
									$chs_save = array('type'=>ucwords($msg), 'stage'=>$chs_stage+1);
								} else if($chs_stage == 3){
									$int_duration = (int)$msg;
									$chs_save = array('duration'=>$int_duration, 'stage'=>$chs_stage+1);
								}
								if($chs_save) {
									$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $chs_save);
								}
							}
							
							// increment flag state
							$chs_stage += 1;
							$chs_flag = array('stage'=>$chs_stage);
							$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $chs_flag);
							// check if user triggers to edit any
							$ch_reverse = '';
							$trackText = strtolower($msg);
							if($trackText == 'edit plan'){
								$ch_reverse = array('stage' => 0, 'name'=>'', 'target'=>'', 'type'=>'', 'duration'=>'');
							} 
							if($ch_reverse){
								$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $ch_reverse);
							}
						}
					}
				}
				
				// pull user account cache if recurrent question is involve
				$cache_acc_status = 0;
				$getacc = $this->Crud->read_single('user_id', $user_id, 'ka_bot_acc_cache');
				if(!empty($getacc)){
					foreach($getacc as $acc){
						$cache_acc_status = $acc->status;
						$acc_stage = $acc->stage;
						
						$bank_more_step = array('list0', 'list1', 'list2', 'list3'); // ignore store if bank list is iterated
						
						if($msg != 'create account'){
							if($acc_stage >= 0){
								if(in_array($msg, $bank_more_step)) {
									// skip cache storage, because it bank selection
								} else {
									// check for cache loop edit
									if($msg == 'chg account') { // change account number? return to loop
										//$chg_loop = array('stage'=>1, 'acc_no'=>'', 'bank_id'=>0, 'acc_name'=>'');
										$chg_loop = array('stage'=>1, 'acc_no'=>'');
										$this->Crud->update('user_id', $user_id, 'ka_bot_acc_cache', $chg_loop);
									} else if($msg == 'chg bank') { // change bank? return to loop
										$chg_loop = array('stage'=>2, 'bank_id'=>'');
										$this->Crud->update('user_id', $user_id, 'ka_bot_acc_cache', $chg_loop);
									} else {
										// store successful loop content
										if($acc_stage == 0){
											$acc_save = array('acc_desc'=>ucwords($msg), 'stage'=>$acc_stage+1);
										} else if($acc_stage == 1){
											$acc_save = array('acc_no'=>$msg, 'stage'=>$acc_stage+1);
										} else if($acc_stage == 2){
											$acc_save = array('bank_id'=>(int)$msg, 'stage'=>$acc_stage+1);
										} else if($acc_stage == 3){
											$acc_save = array('acc_name'=>$msg, 'stage'=>$acc_stage+1);
										}
										if($acc_save) {
											if($this->Crud->update('user_id', $user_id, 'ka_bot_acc_cache', $acc_save) > 0){
												// increment flag state
												$acc_stage += 1;
												$acc_flag = array('stage'=>$acc_stage);
												$this->Crud->update('user_id', $user_id, 'ka_bot_acc_cache', $acc_flag);	
											}
										}
									}
								}
							}
						}
					}
				}
				
				// pull user offer cache if recurrent question is involve
				$cache_offer_status = 0;
				$getoffer = $this->Crud->read_single('user_id', $user_id, 'ka_bot_offer_cache');
				if(!empty($getoffer)){
					foreach($getoffer as $off){
						$cache_offer_status = $off->status;
						$offer_stage = $off->stage;
						
						$cat_more_step = array('list0', 'list1'); // ignore store if category list is iterated
						
						if($msg != 'offer'){
							if($offer_stage >= 0){
								if(in_array($msg, $cat_more_step)) {
									// skip cache storage, because it category selection
								} else {
									// check for cache loop edit
									if($msg == 'chgo category') { // change category? return to loop
										$chg_loop = array('stage'=>2, 'com_id'=>0);
										$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $chg_loop);
									} else if($msg == 'chgo product') { // change product link? return to loop
										$chg_loop = array('stage'=>3, 'product_link'=>'');
										$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $chg_loop);
									} else {
										// store successful loop content
										if($offer_stage == 0){
											$offer_save = array('partner_id'=>(int)$msg, 'stage'=>$offer_stage+1);
										} else if($offer_stage == 1){
											$offer_save = array('savings_id'=>(int)$msg, 'stage'=>$offer_stage+1);
										} else if($offer_stage == 2){
											$offer_save = array('com_id'=>(int)$msg, 'stage'=>$offer_stage+1);
										} else if($offer_stage == 3){
											// only save if web link supplied
											if(strpos($msg, 'http://') !== false || strpos($msg, 'https://') !== false) {
												$offer_save = array('product_link'=>$msg, 'stage'=>$offer_stage+1);
											}
										}
										if($offer_save) {
											if($this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $offer_save) > 0){
												// increment flag state
												$offer_stage += 1;
												$offer_flag = array('stage'=>$offer_stage);
												$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $offer_flag);	
											}
										}
									}
								}
							}
						}
					}
				}
				
			}
		}
		
		// check if its a recurrent process for savings plan
		if($cache_saving_status == 1) {
			//suspend offer loops to aviod conflict, because user can be transferred from offer
			$cache_offer_status = 0;
			if($msg == 'cancel plan') { /////// CANCEL PLAN LOOP
				$answer = $this->get_create_plan($user_id, 'cancel');
			} else if($msg == 'done') { /////// COMPLETE AND SAVE A PLAN
				$answer = $this->get_create_plan($user_id, 'done');
			} else if($msg == 'start a plan' || $msg != '') { /////// PROCESS PLAN LOOP
				$answer = $this->get_create_plan($user_id, 'new');
			}
		}
		
		// check if its a recurrent process for account
		if($cache_acc_status == 1) {
			if($msg == 'cancel account') { /////// CANCEL ACCOUNT LOOP
				$answer = $this->get_create_account($user_id, 'cancel');
			} else if($msg == 'done') { /////// COMPLETE AND SAVE ACCOUNT
				$answer = $this->get_create_account($user_id, 'done');
			} else if($msg == 'create account' || $msg != '') { /////// PROCESS ACCOUNT LOOP
				$answer = $this->get_create_account($user_id, 'new', $msg, $senderId);
			}
		}
		
		// check if its a recurrent process for offer
		if($cache_offer_status == 1) {
			if($msg == 'cancel offe') { /////// CANCEL OFFER LOOP
				$answer = $this->get_create_offer($user_id, 'cancel');
			} else if($msg == 'done') { /////// COMPLETE AND SAVE OFFER
				$answer = $this->get_create_offer($user_id, 'done');
			} else if($msg == 'create offer' || $msg != '') { /////// PROCESS ACCOUNT LOOP
				$answer = $this->get_create_offer($user_id, 'new', $msg, $senderId);
			}
		}
		
		// response AI block flow - Intents
		if($msg == 'lol' || $msg == 'smile') { 
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array('text' => 'Lol =) - Good, I\'m glad is makes you smile. (y)'.$this->bot_email);
		} else if($msg == '(y)' || $msg == 'thanks' || $msg == 'thank you') { 
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array('text' => 'Lol =) - Thanks, appreciate. (y)');
		} else if($msg == 'welcome') {  /////// GET STARTED
			if($this->user_othername != ''){$gttx = "Hi ".$this->user_othername.'. ';} else {$gttx = '';}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array ("text" => $gttx."Welcome to SusuAI bot, your Artificial Intelligence Savings Plan. You can say 'hi' or 'hello'");
		} else if($msg == 'bye') {  /////// BYE
			if($this->user_othername != ''){$gttx = "OK ".$this->user_othername.'. ';} else {$gttx = '';}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array ("text" => $gttx."Have a nice time, see you some other time.");
		} else if($msg == 'help' || $msg == 'get help') {  /////// GET HELP
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_help();
		} else if($msg == 'status') { /////// LOGIN STATUS
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('status', $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_login_status($this->user_othername, $this->user_lastname, $this->user_email, $this->user_phone);
			}
		} else if($msg == 'logout' || $msg == 'out') { /////// LOGOUT/UNLINK ACCOUNT
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('status', $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->remove_auth('continue', $senderId);
			}
		} else if($msg == 'hi' || $msg == 'hello' || $msg == 'hey' || $msg == 'sup') { /////// GREETING
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_greeting();
		} else if($msg == 'what it is' || $msg == 'What can you do?') { /////// WHAT IT IS
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_what_it_is();
		} else if($msg == 'bot keywords' || $msg == 'keywords' || strpos($msg, 'help') !== false) { /////// BOT KEYWORDS
			// format instanct response
			if(strpos($msg, 'help') !== false){
				$log_msg = array('text' => 'Check if these Keywords can help...');
			} else {
				$log_msg = array('text' => 'Fetching keywords...');
			}
			if($log_msg){$this->_send_message($senderId, $log_msg);}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_keywords();
		} else if($msg == 'get started' || $msg == 'start' || $msg == 'continue') { /////// START CHATTING
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_start();
		} else if(strpos($msg, '~setcountry') !== false) { /////// SET COUNTRY
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = $this->get_set_country($user_id, $msg);
		} else if($msg == 'create a plan' || $msg == 'create plan' || $msg == 'plan') { /////// CREATE A PLAN
			// ask user to change country for currency format
			if($user_country == 0) {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_country();
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_create_plan($user_id, 'new');
			}
		} else if($msg == 'all savings') { /////// ALL SAVINGS STATUS
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('savings', $senderId);
			} else {
				$log_msg = array("text" => "B-) Getting all your savings plan... (y)");
				if($log_msg){$this->_send_message($senderId, $log_msg);}
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_all_savings($user_id);
			}
		} else if($msg == 'savings' || $msg == 'savings status') { /////// LIST SAVINGS STATUS
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('savings', $senderId);
			} else {
				// instant notification
				$log_msg = array("text" => "B-) Fetching your savings list... (y)");
				if($log_msg){$this->_send_message($senderId, $log_msg);}
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_list_savings($user_id);
			}
		} else if($msg == 'check my vault' || $msg == 'my vault' || $msg == 'vault') { /////// CHECK VAULT
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('my vault', $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_vault($user_id);
			}
		} else if(in_array($msg, $user_saving_list)) { /////// EACH SAVINGS STATUS
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('savings', $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_each_savings_status($user_id, $msg);
			}
		} else if(strpos($msg, 'delete') !== false) { /////// DELETE SAVINGS
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth($msg, $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_delete_savings($user_id, $senderId, $msg);
			}
		} else if(strpos($msg, 'create account') !== false) { /////// CREATE ACCOUNT
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth($msg, $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_create_account($user_id, 'new');
			}
		} else if($msg == 'disbursement accounts') { /////// ALL DISBURSED ACCOUNT
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('disbursement accounts', $senderId);
			} else {
				$log_msg = array("text" => "B-) Getting all your disbursement accounts... (y)");
				if($log_msg){$this->_send_message($senderId, $log_msg);}
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_all_accounts($user_id);
			}
		} else if(in_array($msg, $user_acc_list)) { /////// EACH DISBURSED ACCOUNT
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth('disbursement accounts', $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_each_account($user_id, $msg);
			}
		} else if(strpos($msg, 'link') !== false) { /////// LINK SAVINGS TO ACCOUNT
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth($msg, $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_link_savings_to_account($user_id, $senderId, $msg);
			}
		} else if(strpos($msg, 'offer') !== false) { /////// CREATE OFFERS
			// check if its auth
			if($user_status == 0 && $user_last_log!=''){
				$answer = $this->request_auth($msg, $senderId);
			} else {
				$this->_progress($senderId, 'typing_on'); // progress bar
				$answer = $this->get_create_offer($user_id, 'new');
			}
		} else if(strpos($msg, 'your name') !== false) {  /////// WHATS YOUR NAME
			if($this->user_othername != ''){$gttx = "Thanks ".$this->user_othername.'. ';} else {$gttx = '';}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array ("text" => $gttx."My name is SusuAI, your savings assistance. :)");
		} else if(strpos($msg, 'you from') !== false) {  /////// WHERE ARE YOU FROM
			if($this->user_othername != ''){$gttx = "Thanks ".$this->user_othername.'. ';} else {$gttx = '';}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array ("text" => $gttx."I'm from your phone on Messenger :), I go everywhere you go.");
		} else if(strpos($msg, 'you born') !== false || strpos($msg, 'date of birth') !== false || strpos($msg, 'dob') !== false) {  /////// DOB
			if($this->user_othername != ''){$gttx = "Thanks ".$this->user_othername.'. ';} else {$gttx = '';}
			$this->_progress($senderId, 'typing_on'); // progress bar
			$answer = array ("text" => $gttx.":), was born somethings in March 2017.");
		} 
		
		// send response here
		if($msg) {
			$this->_send_message($senderId, $answer);
		}
	}
	
	/////////// SEND MESSEGE //////
	private function _send_message($sender='', $answer='') {
		$response = array(
			'recipient' => array('id' => $sender),
			'message' => $answer
		);
		$this->Crud_messenger->message($this->accessToken, $response);
	}
	
	/////////// PROGRESS BAR //////
	private function _progress($sender='', $answer='') {
		$response = array(
			'recipient' => array('id' => $sender),
			'sender_action' => $answer
		);
		$this->Crud_messenger->message($this->accessToken, $response);
	}
	
	/////// GREETING ////////////
	private function get_greeting(){
		// do welcome options here
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'generic',
					'elements' => array(
						array(
							'title' => 'Welcome to SusuAI',
							'item_url' => '',
							'image_url' => 'https://susu-ai.com/landing/img/bg_small.jpg',
							'subtitle' => 'Your Artificial Intelligence Savings Plan',
							'buttons' => array(
								array(
									'type' => 'postback',
									'title' => 'What It Is?',
									'payload' => 'what it is'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Help',
									'payload' => 'get help'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						)
					)
				)
			)
		 );	
		 return $answer;
	}
	
	/////// WHAT IT IS ////////////
	private function get_what_it_is(){
		// do what is it options here
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'generic',
					'elements' => array(
						array(
							'title' => 'Welcome to SusuAI',
							'item_url' => 'https://susu-ai.com/',
							'image_url' => 'https://susu-ai.com/landing/img/iphone_img.png',
							'subtitle' => 'SusuAI will help you plan and automate all your savings and take care of all your bills.',
							'buttons' => array(
								array(
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/',
									'title' => 'Check It Out!',
									'webview_height_ratio' => 'tall'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						),
						array(
							'title' => 'FAQ',
							'item_url' => 'https://susu-ai.com/faq',
							'image_url' => 'https://susu-ai.com/assets/images/faq.png',
							'subtitle' => 'The Frequently Asked Questions to few of what I think you might need to know.',
							'buttons' => array(
								array(
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/faq',
									'title' => 'See FAQ',
									'webview_height_ratio' => 'tall'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						),
						array(
							'title' => 'Bot Keywords',
							'item_url' => '',
							'image_url' => 'https://susu-ai.com/assets/images/keyword.png',
							'subtitle' => 'See the Bot Keywords to help you understand the flow and how to use them.',
							'buttons' => array(
								array(
									'type' => 'postback',
									'title' => 'Bot Keywords',
									'payload' => 'bot keywords'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						),
						array(
							'title' => 'Privacy Policy',
							'item_url' => 'https://susu-ai.com/privacy',
							'image_url' => 'https://susu-ai.com/assets/images/privacy.png',
							'subtitle' => 'See how your privacy is protected and how to apply them.',
							'buttons' => array(
								array(
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/privacy',
									'title' => 'Read Policy',
									'webview_height_ratio' => 'tall'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						),
						array(
							'title' => 'Terms of Services',
							'item_url' => 'https://susu-ai.com/terms',
							'image_url' => 'https://susu-ai.com/assets/images/terms.png',
							'subtitle' => 'Check out the Do and Don\'t before using this service.',
							'buttons' => array(
								array(
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/terms',
									'title' => 'Read Terms',
									'webview_height_ratio' => 'tall'
								),
								array(
									'type' => 'postback',
									'title' => 'Get Started',
									'payload' => 'get started'
								)
							)
						)
					)
				)
			)
		 );	
		 return $answer;
	}
	
	/////// BOT KEYWORDS ////////////
	private function get_keywords(){
		// display all bot keywords here, in batches because of facebook limit
		$answer1 = array(
			'text' => "When you TYPE below Keywords:\n============== \n\n'Welcome' => Display a welcome message, telling you to say 'Hi' or 'Hello'.
			\n'Hi or Hello' => Display welcome options 'What It Is', 'Get Help', 'Get Started'.
			\n'What It Is' => Display options to let you know more about this Bot, such as 'Check It Out', 'FAQ', 'Keywords', 'Privacy' and 'Terms'.
			\n'Get Help or Help' => Display an help options 'General Support' and 'Technical Support' for direct help.
			\n'Get Started or Continue' => Display Options and ask if you want to 'Create A Plan', 'Savings Status' or 'Vault'.
			\n'Status' => Display your login status.");
		$this->_send_message($this->senderId, $answer1);
		
		$answer2 = array(
			'text' => "'Create A Plan' => Display a walkthrough steps to create your savings plan.
			\n'Savings Status or All Savings' => Display all your savings, and tapping on each gives you detailed status.
			\n'Savings' => Display only 4 savings in list, provided you have two or more savings, else it will display in button.
			\n'Check My Vault or Vault' => Display your Vault/Wallet summary such Total Savings, Disbused, and Balance.
			\n'Savings Name [e.g. Buy Phone]' => Display details about that particular savings. Suppose you type 'Rent' means you already have Rent savings, it will display details about your Rent savings.");
		$this->_send_message($this->senderId, $answer2);
		
		$answer = array(
			'text' => "'Delete [Plan Name]' => To delete a plan. E.g. Delete Buy Phone will completely delete Buy Phone savings from your plan list.
			\n'Create Account' => Display a walkthrough steps to create disburse account.
			\n'Disbursment Accounts' => Displays all disbursed account created.
			\nLink Account => Link a Saving Plan to Disbursed Account. E.g. Link House Rent to My Landlord.",
			'quick_replies' => array(
				array(
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'continue',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				)
			)
			
		);	
		 return $answer;
	}
	
	/////// GET HELP ////////////
	private function get_help(){
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'button',
					'text' => 'For further assistance? Call and Talk to a representative',
					'buttons' => array(
						array(
							'type' => 'phone_number',
							'title' => 'General Supports',
							'payload' => '+2348033311990'
						),
						array(
							'type' => 'phone_number',
							'title' => 'Technical Supports',
							'payload' => '+2348058917364'
						),
						array(
							'type' => 'postback',
							'title' => 'Get Started',
							'payload' => 'get started'
						)
					)
				)
			)
		 );	
		 return $answer;
	}
	
	/////// GET START ////////////
	private function get_start(){
		$answer = array(
			'text' => "What would you like to do?",
			'quick_replies' => array(
				array(
					'content_type' => 'text',
					'title' => 'Create A Plan',
					'payload' => 'Create a plan',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Savings Status',
					'payload' => 'savings status',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Check My Vault',
					'payload' => 'check my vault',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Disbursement',
					'payload' => 'disbursement accounts',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Offers',
					'payload' => 'offer',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				)
			)
		 );	
		 return $answer;
	}
	
	/////// ALL COUNTRY ////////////
	private function get_country(){
		// get serving countries
		$country_list = array();
		$getct = $this->Crud->read_order('ka_country', 'name', 'ASC');
		if(!empty($getct)){
			foreach($getct as $ct){
				$ct_id = $ct->id;
				$ct_name = $ct->name;
				$ct_flag = $ct->flag;
				
				$country_list[] = array(
					'content_type' => 'text',
					'title' => $ct_name,
					'payload' => '~setcountry | '.$ct_id,
					'image_url' => 'https://susu-ai.com/'.$ct_flag
				);
			}
		}
		
		$answer = array(
			'text' => "Hey! please pick your Country so I can format your savings currency. \n\nPlease Note: You will not be able to revert this.",
			'quick_replies' => $country_list
		 );
		return $answer;
	}
	
	/////// SET COUNTRY ////////////
	private function get_set_country($user_id, $msg){
		// set user country
		$text = '';
		$get_msg = explode(' | ', $msg); // break reply to capture country id
		$get_id = $get_msg[1];
		
		$save_data = array('country' => $get_id);
		$save_it = $this->Crud->update('id', $user_id, 'ka_user', $save_data);
		if($save_it <= 0) {
			$text = "Oops! - There is problem, please try later or check your connectivity";
		} else {
			$getcurr = $this->Crud->country_data($get_id, 'currency'); // get country currency
			$text = "Yaw! - you have successfully set your country, and I will format all your savings currency as ".$getcurr;
		}
		
		
		$answer = array(
			'text' => $text,
			'quick_replies' => array (
				array (
					'content_type' => 'text',
					'title' => 'Create A Plan',
					'payload' => 'Create a plan',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array (
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'continue',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				)
			)
		 );
		return $answer;
	}
	
	/////// CREATE/CANCEL PLAN ////////////
	private function get_create_plan($user_id='', $job=''){
		///// do new savings plan cache here
		$curr = $this->user_currency;
		if($job == 'new') {
			// check user cache for savings
			$checkcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_p_cache');
			if(empty($checkcache)){
				// create cache for user
				$cache_data = array(
					'user_id' => $user_id,
					'status' => 1, // use this to track user stage in chat
					'stage' => 0 // use this to flag the state of flow
				);
				$this->Crud->create('ka_bot_p_cache', $cache_data);
				$answer = array(
					'text' => "Thank you! I will walk you through it in just few steps. Just Tap/Click 'Start Plan'",
					'quick_replies' => array(
						array(
							'content_type' => 'text',
							'title' => 'Start Plan',
							'payload' => 'Start Plan',
							'image_url' => 'https://susu-ai.com/assets/images/green.png'
						),
						array(
							'content_type' => 'text',
							'title' => 'Cancel Plan',
							'payload' => 'Cancel Plan',
							'image_url' => 'https://susu-ai.com/assets/images/red.png'
						)
					)
				);
			} else {
				foreach($checkcache as $cache){
					$name = $cache->name;
					$target = $cache->target;
					$type = $cache->type;
					$duration = $cache->duration;
					$stage = $cache->stage;
					
					// convert target to whole integer
					$target = preg_replace('/\s+/', '', $target); // remove all in between white spaces
					$target = str_replace(',', '', $target); // remove money format
					$target = floatval($target);
					
					if($stage == 0){
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
							'stage' => 0 // use this to flag the state of flow
						);
						$answer = array(
							'text' => "Thank you! I will walk you through it in just few steps. Just Tap/Click 'Start Plan'",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Start Plan',
									'payload' => 'Start Plan',
									'image_url' => 'https://susu-ai.com/assets/images/green.png'
								),
								array(
									'content_type' => 'text',
									'title' => 'Cancel Plan',
									'payload' => 'Cancel Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								)
							)
						);
					} else {
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
						);
					}
					
					// update cache status here
					$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $cache_data);
					
					if($name!='' && $target!='' && $type!='' && $duration!=0){ // all completely supplied
						$break_amt = $target / $duration;
						$break_amt = $curr.number_format($break_amt,2);
						$target = $curr.number_format($target,2);
						if($duration > 1){$dura = 's';} else {$dura = '';}
						if($type == 'Monthly'){
							$duration_text = $duration.' Month'.$dura; 	
						} else if($type == 'Weekly'){
							$duration_text = $duration.' Week'.$dura; 	
						} else if($type == 'Daily'){
							$duration_text = $duration.' Day'.$dura; 	
						}
						
						$answer = array(
							'text' => "Congratulations =) You just completed the ".ucwords($name)." savings plan walkthrough. Review details below:\n\nSavings Plan Name: ".ucwords($name)." \nTarget: ".$target." \nDuration: ".$duration_text." \nContribution: ".$break_amt." ".$type,
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Done',
									'payload' => 'done',
									'image_url' => 'https://susu-ai.com/assets/images/green.png'
								),
								array(
									'content_type' => 'text',
									'title' => 'Edit Plan',
									'payload' => 'Edit Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								)
							)
						 );	
					} else if($name=='') { // supply savings name
						$off_loop = array('stage'=>0);
						$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $off_loop);
						$answer = array(
							'text' => "Please name your Savings (e.g. Buy Phone, Holiday or School Fees, etc.)",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Cancel Plan',
									'payload' => 'Cancel Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								)
							)
						);
					} else if($target=='') { // supply target
						$off_loop = array('stage'=>1);
						$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $off_loop);
						$answer = array(
							'text' => "Please what's your Target/Budget for ".ucwords($name)." savings (e.g. 85000)",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Cancel Plan',
									'payload' => 'Cancel Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								)
							)
						);
					} else if($type=='') { // supply type
						$off_loop = array('stage'=>2);
						$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $off_loop);
						$answer = array(
							'text' => "How would you like to be doing your ".ucwords($name)." savings?",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Daily',
									'payload' => 'Daily',
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								),
								array(
									'content_type' => 'text',
									'title' => 'Weekly',
									'payload' => 'Weekly',
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								),
								array(
									'content_type' => 'text',
									'title' => 'Monthly',
									'payload' => 'Monthly',
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								),
								array(
									'content_type' => 'text',
									'title' => 'Cancel Plan',
									'payload' => 'Cancel Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								)
							)
						 );	
					} else if($duration==0) { // supply duration
						$off_loop = array('stage'=>3);
						$this->Crud->update('user_id', $user_id, 'ka_bot_p_cache', $off_loop);
						$answer = array(
							'text' => "How long do you wish to complete your ".ucwords($type)." ".ucwords($name)." savings (e.g. 12)",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Cancel Plan',
									'payload' => 'Cancel Plan',
									'image_url' => 'https://susu-ai.com/assets/images/red.png'
								),
							)
						);
					}
				}
			}
		} else if($job == 'done'){
			// now clear user savings plan cache
			$savecache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_p_cache');
			if(!empty($savecache)){
				foreach($savecache as $scache){
					$name = $scache->name;
					$target = $scache->target;
					$type = $scache->type;
					$duration = $scache->duration;
					
					// convert target to whole integer
					$target = preg_replace('/\s+/', '', $target); // remove all in between white spaces
					$target = str_replace(',', '', $target); // remove money format
					$target = floatval($target);	
					
					// check if user already have savings
					$chkmysave = $this->Crud->check2('user_id', $user_id, 'name', $name, 'ka_personal');
					if($chkmysave > 0){
						$answer = array(
							'text' => "Oops! - You already have this savings registered",
							'quick_replies' => array(
								array(
									'content_type' => 'text',
									'title' => 'Continue',
									'payload' => 'Continue',
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								)
							)
						);
					} else {
						// now register cache data in database
						$savings = $target / $duration;
						$save_c_data = array(
							'user_id' => $user_id,
							'name' => $name,
							'target' => $target,
							'type' => $type,
							'duration' => (int)$duration,
							'saving' => $savings,
							'reg_date' => date(fdate)
						);	
						$save_p_id = $this->Crud->create('ka_personal', $save_c_data);
						if($save_p_id){
							// delete the savings cache
							$this->Crud->delete('user_id', $user_id, 'ka_bot_p_cache');
							$answer = array(
								'attachment' => array(
									'type' => 'template',
									'payload' => array(
										'template_type' => 'button',
										'text' => "Great! (y) - Your Savings Plan created successfully.\n\nSEE HINTS:\n=========\nTo Start Your ".$type." contributions: Tap/Click 'Start Contributing'\n\nTo Link Disbursement Account: Tap/Click 'Continue' and then 'Disbursement'\n\nTo Get Upto 7% Money Back: Tap/Click 'Continue' and then 'Offers'",
										'buttons' => array(
											array(
												'type' => 'web_url',
												'url' => 'https://susu-ai.com/savings/personal/p/'.$save_p_id.'?sender='.$this->senderId,
												'title' => 'Start Contributing',
												'webview_height_ratio' => 'tall'
											),
											array(
												'type' => 'postback',
												'title' => 'Continue',
												'payload' => 'Continue'
											)
										)
									)
								)
							 );
						}
					}
				}
			}
		} else if($job == 'cancel'){
			// now clear user savings plan cache
			$clearcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_p_cache');
			if(!empty($clearcache)){
				$this->Crud->delete('user_id', $user_id, 'ka_bot_p_cache');
				$answer = array(
					'text' => "Oops! - Your Savings Plan walkthrough is cleared",
					'quick_replies' => array(
						array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'Continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						)
					)
				);
			}
		}
		return $answer;
	}
	
	/////// ALL SAVINGS SAVINGS ////////////
	private function get_all_savings($user_id=''){
		// get user personal savings
		$saving_status_list = array();
		$getpers = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
		if(!empty($getpers)){
			foreach($getpers as $pers){
				$pers_id = $pers->id;
				$pers_name = $pers->name;
				$pers_duration = $pers->duration;
				$pers_cycle = $pers->cycle;
				$pers_active = $pers->active;
				
				if($pers_active == 0){
					$pers_icon = 'https://susu-ai.com/assets/images/red.png';
				} else {
					$pers_icon = 'https://susu-ai.com/assets/images/green.png';
				}
				
				//if($saving_status_list != ''){$saving_status_list .= ',';}
				$saving_status_list[] = array(
					'content_type' => 'text',
					'title' => ucwords($pers_name),
					'payload' => ucwords($pers_name),
					'image_url' => $pers_icon
				);
			}
		}
		
		if(!empty($saving_status_list)){
			$answer = array(
				'text' => 'Which of the Savings would you like to check?',
				'quick_replies' => $saving_status_list
			 );	
		} else {
			$answer = array(
				'text' => "B-) Your Savings Plan is empty in my head, you will need to Create A Savings plan (Y)",
				'quick_replies' => array(
					array(
						'content_type' => 'text',
						'title' => 'Create A Plan',
						'payload' => 'Create A Plan',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					)
				)
			);
		}
		return $answer;
	}
	
	/////// LIST SAVINGS SAVINGS ////////////
	private function get_list_savings($user_id=''){
		// get user personal savings
		$curr = $this->user_currency;
		$saving_status_list = array();
		$saving_status_button = array();
		$ls_count = 0;
		$getpers = $this->Crud->read_single_order('user_id', $user_id, 'ka_personal', 'active', 'DESC');
		if(!empty($getpers)){
			foreach($getpers as $pers){
				$pers_id = $pers->id;
				$pers_name = $pers->name;
				$pers_duration = $pers->duration;
				$pers_type = $pers->type;
				$pers_saving = $curr.number_format((float)$pers->saving,2);
				$pers_target = $curr.number_format((float)$pers->target,2);
				$pers_start = date('d M, Y', strtotime($pers->saving_start));
				$pers_next = date('d M, Y', strtotime($pers->saving_next));
				$pers_end = date('d M, Y', strtotime($pers->saving_end));
				$pers_cycle = $pers->cycle;
				$pers_active = $pers->active;
				$pers_expired = $pers->expired;
				$pers_complete = $pers->complete;
				$pers_disbursed = $pers->disbursed;
				
				if($pers_active == 0){
					$es_status = 'Inactive';
					if($pers_complete == 1){$es_status = 'Completed';}	
				} else {
					$es_status = 'Active';
					if($pers_complete == 0){$es_status = 'In-progress';}
				}
				
				///////////// LIST TEMPLATE ////////
				$sub_title = "Savings of ".$pers_saving." ".$pers_type." is currently at ".$pers_cycle." of ".$pers_duration." Cycles";
				if($pers_complete == 0) { // only non-completed savings
					if($ls_count <= 3) { // facebook only accept max of 4 in list
						$saving_status_list[] = array (
							'title' => ucwords($pers_name).' ('.$es_status.')',
							'image_url' => 'https://susu-ai.com/landing/img/iphone_img.png',
							'subtitle' => $sub_title,
							'default_action' => array (
								'type' => 'web_url',
								'url' => 'https://susu-ai.com/savings/personal/p/'.$pers_id.'?sender='.$this->senderId,
								'webview_height_ratio' => 'tall',
							),
							'buttons' => array (
								array (
									'title' => 'Contribute Now',
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/savings/personal/p/'.$pers_id.'?sender='.$this->senderId,
									'webview_height_ratio' => 'tall',
								),
							),
						);
					}
					
					$ls_count += 1; // increament the list count
				}
				
				/////////// QUICK REPLY TEMPLATE -- Because Facebook only accept min. of 2 to display list template
				if($pers_active == 0){
					$pers_icon = 'https://susu-ai.com/assets/images/red.png';
				} else {
					$pers_icon = 'https://susu-ai.com/assets/images/green.png';
				}
				
				$saving_status_button[] = array(
					'content_type' => 'text',
					'title' => ucwords($pers_name),
					'payload' => ucwords($pers_name),
					'image_url' => $pers_icon
				);
			}
		}
		
		if(!empty($saving_status_list) ){
			if(count($saving_status_list) >= 2) { // facebook said it list must be atleat 2
				$answer = array (
					'attachment' => array (
						'type' => 'template',
						'payload' => array (
							'template_type' => 'list',
							'elements' => $saving_status_list,
							'buttons' => array (
								array (
									'title' => 'All Savings',
									'type' => 'postback',
									'payload' => 'all savings'
								)
							)
						),
					)
				 );	
			} else { // if list not upto 2, then display quick reply
				$answer = array(
					'text' => 'Which of the Savings would you like to check?',
					'quick_replies' => $saving_status_button
				 );	
			}
		} else {
			$answer = array(
				'text' => "B-) Your Savings Plan is empty in my head, you will need to Create A Savings plan (Y)",
				'quick_replies' => array(
					array(
						'content_type' => 'text',
						'title' => 'Create A Plan',
						'payload' => 'Create A Plan',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					),
					array(
						'content_type' => 'text',
						'title' => 'Continue',
						'payload' => 'continue',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					)
				)
			);
		}
		return $answer;
	}
	
	/////// EACH SAVINGS STATUS ////////////
	private function get_each_savings_status($user_id='', $saving=''){
		// get user personal savings
		$curr = $this->user_currency;
		$contr_btn = array();
		$linked_account = 'None';
		$linked_offer = 'None';
		$hint_info = '';
		$each_savin_list = 'I can\'t find any saving in your account, try and create one.';
		$getpers = $this->Crud->read2('user_id', $user_id, 'name', $saving, 'ka_personal');
		if(!empty($getpers)){
			foreach($getpers as $pers){
				$pers_id = $pers->id;
				$pers_name = $pers->name;
				$pers_duration = $pers->duration;
				$pers_type = $pers->type;
				$pers_saving = $curr.number_format((float)$pers->saving,2);
				$pers_target = $curr.number_format((float)$pers->target,2);
				$pers_start = date('d M, Y', strtotime($pers->saving_start));
				$pers_next = date('d M, Y', strtotime($pers->saving_next));
				$pers_end = date('d M, Y', strtotime($pers->saving_end));
				$pers_cycle = $pers->cycle;
				$pers_active = $pers->active;
				$pers_expired = $pers->expired;
				$pers_complete = $pers->complete;
				$pers_disbursed = $pers->disbursed;
				
				if($pers_active == 0){
					$es_status = 'Inactive';
					$hint_info .= "To Start Your ".$pers_type." Contributions: Tap/Click 'Start Contributing'\n\n";
					if($pers_complete == 1){$es_status = 'Completed'; $hint_info = '';}	
				} else {
					$es_status = 'Active';
					if($pers_complete == 0){$es_status = 'In-progress';}
				}
				
				if($pers_cycle > 0){
					$date_count = "\nStart Date: ".$pers_start.", \nNext Date: ".$pers_next.", \nEnd Date: ".$pers_end;
				} else {
					$date_count = '';
				}
				
				// get linked savings
				$getlinked = $this->Crud->read2('user_id', $user_id, 'saving_id', $pers_id, 'ka_account_link');
				if(!empty($getlinked)){
					foreach($getlinked as $glink) {
						// get account name
						$getacc = $this->Crud->read_single('id', $glink->acc_id, 'ka_account');
						if(!empty($getacc)){
							foreach($getacc as $acc) {
								$linked_account = ucwords($acc->acc_desc);
							}
						}
					}
				} else {
					$hint_info .= "To Link Disbursement Account: Tap/Click 'Continue' and then 'Disbursement'\n\n";	
				}
				
				// get linked offer
				$getoffer = $this->Crud->read2('user_id', $user_id, 'saving_id', $pers_id, 'ka_offer');
				if(!empty($getoffer)){
					foreach($getoffer as $goffer) {
						$off_interest = $goffer->interest;
						// get offer details
						$getcom = $this->Crud->read_single('id', $goffer->com_id, 'ka_offer_commission');
						if(!empty($getcom)){
							foreach($getcom as $gcom) {
								$off_name = $gcom->name;
								$off_com = $gcom->com;
								
								// get partner name
								$getpart = $this->Crud->read_single('id', $gcom->partner_id, 'ka_offer_partner');
								if(!empty($getpart)){
									foreach($getpart as $gpart) {
										$off_partner = $gpart->name;
										
										$linked_offer = $off_partner.' - '.$off_name.' ('.$off_com.'% Money Back)';
									}
								}
							}
						}
					}
				} else {
					$hint_info .= "To Get Upto 7% Money Back: Tap/Click 'Continue' and then 'Offers'";	
				}
				
				if($hint_info != '' && $pers_cycle <= 0){
					$hint_info = "\n\nSEE HINTS:\n==========\n".$hint_info;	
				} else {
					$hint_info = '';
				}
				
				$each_savin_list = "Your ".ucwords($pers_name)." Savings is presently at ".$pers_cycle." of ".$pers_duration." Cycle with ".$pers_saving." ".$pers_type." Contribution to reach a target of ".$pers_target.". \n\nPlan Status: ".$es_status.". ".$date_count."\nDisbursement Account: ".$linked_account."\nSubscribed Offer: ".$linked_offer.$hint_info;
				
				// check if contribution made already
				$pers_today = strtotime(date('Y-m-d'));
				$pers_current = strtotime(date('Y-m-d', strtotime($pers_next)));
				$date_diff = $pers_today - $pers_current;
				$date_diff = floor($date_diff / (60 * 60 * 24));
				
				// now show button if it's time for contribution
				if($pers_cycle <= 0) { // show contribute button for Inactive savings
					$contr_btn[] = array(
						'type' => 'web_url',
						'url' => 'https://susu-ai.com/savings/personal/p/'.$pers_id.'?sender='.$this->senderId,
						'title' => 'Start Contributing',
						'webview_height_ratio' => 'tall'
					);
				} else if($date_diff >= 0) { // show contribute button for due cycle
					if($pers_complete == 0){ // show contribute button if not completed
						$contr_btn[] = array(
							'type' => 'web_url',
							'url' => 'https://susu-ai.com/savings/personal/p/'.$pers_id.'?sender='.$this->senderId,
							'title' => 'Contribute Now',
							'webview_height_ratio' => 'tall'
						);
					} else {
						if($pers_disbursed == 0){ // show withdraw button if not disbused
							$contr_btn[] = array(
								'type' => 'web_url',
								'url' => 'https://susu-ai.com/savings/personal/w/'.$pers_id.'?sender='.$this->senderId,
								'title' => 'Withdraw Now',
								'webview_height_ratio' => 'tall'
							);
						}
					}
				}
				
				// show delete button to only fresh and inactive savings
				if($pers->saving_start == ''){
					$contr_btn[] = array(
						'type' => 'postback',
						'title' => 'Delete Plan',
						'payload' => 'Delete '.ucwords($pers_name)
					);
				}
				
				$contr_btn[] = array(
					'type' => 'postback',
					'title' => 'Continue',
					'payload' => 'Continue'
				);
			}
		}
		
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'button',
					'text' => $each_savin_list,
					'buttons' => $contr_btn
				)
			)
		 );		
		return $answer;
	}
	
	/////// DELETE SAVINGS ////////////
	private function get_delete_savings($user_id='', $senderId='', $msg=''){
		// check the delete intent stage
		$answer = '';
		$del_plan = '';
		$msg_intent = explode(' ', trim($msg)); // break delete into phrases
		if(count($msg_intent) <= 1 || empty($msg_intent)) { // means only delete is in intent
			$answer = array(
				'text' => ":O Awwz...you need to tell me the Plan you want to delete. It must be this format 'Delete [Plan Name]', e.g. Delete Buy Phone",
				'quick_replies' => array(
					array(
						'content_type' => 'text',
						'title' => 'Savings',
						'payload' => 'all savings',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					),
					array(
						'content_type' => 'text',
						'title' => 'Continue',
						'payload' => 'continue',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					)
				)
			);
		} else {
			// first get the real item and remove Delete
			for($i=1; $i<count($msg_intent); $i++) {
				// don't add space after last item
				if($i == count($msg_intent)) {
					$del_plan .= $msg_intent[$i];
				} else {
					$del_plan .= $msg_intent[$i].' ';
				}
			}
			
			$del_plan = trim($del_plan);
			
			// now check if confirmation is added
			if(strpos($del_plan, ':~') !== false) {
				$del_array = explode(':~', $del_plan);
				$del_item = $del_array[0];
				$del_resp = $del_array[1];
				$resp_text = '';
				
				if($del_resp == 'no') {
					$resp_text = ":) Great! - You nearly broke my heart <3, Lolz...I will keep it for you";
				} else {
					// check if saving exist and it's only inactive plan
					$checkplan = $this->Crud->read2('user_id', $user_id, 'name', $del_item, 'ka_personal');
					if(empty($checkplan)){
						$resp_text = "O:) Oops! I can't find ".ucwords($del_item)." in your Plan list.";
					} else {
						foreach($checkplan as $cp){
							$save_id = $cp->id;
							$saving_start = $cp->saving_start;
							if($saving_start != '') {
								$resp_text = ":P Oh! Sorry, you can only delete Savings Plan that has no contribution history";
							} else {
								if($this->Crud->delete('id', $save_id, 'ka_personal') > 0) {
									$resp_text = ucwords($del_item)." has been deleted from your Savings Plan list. :'(";
								} else {
									$resp_text = "O:) Oops! Something went wrong, please try later or check network connectivity.";
								}
							}
						}
					}
				}
				
				$answer = array(
					'text' => $resp_text,
					'quick_replies' => array(
						array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						)
					)
				);
			} else { // ask user for confirmation here
				$payload_yes = 'delete '.$del_plan.':~yes';
				$payload_no = 'delete '.$del_plan.':~no';
				$answer = array(
					'text' => ":P Hmmm...are you really sure you want to delete ".ucwords($del_plan),
					'quick_replies' => array(
						array(
							'content_type' => 'text',
							'title' => 'Yes',
							'payload' => $payload_yes,
							'image_url' => 'https://susu-ai.com/assets/images/green.png'
						),
						array(
							'content_type' => 'text',
							'title' => 'No',
							'payload' => $payload_no,
							'image_url' => 'https://susu-ai.com/assets/images/red.png'
						)
					)
				);
			}
		}
		return $answer;
	}
	
	/////// VAULT STATUS ////////////
	private function get_vault($user_id=''){
		// send pre-message and explain Vaults meaning
		$ans = array("text" => "Vault/Wallet gives summary of your savings accounts: SAVINGS (savings plan purspose), OFFERS (promotion/money give back, approved funds auto moved to Voluntary Vault), and VOLUNTARY (occasional savings for emergency)");
		$this->_send_message($this->senderId, $ans);
		
		// get user vault savings
		$curr = $this->user_currency;
		$empty_vault = true;
		$vault_save = 0;
		$vault_debit = 0;
		$vault_total = 0;
		$getvault = $this->Crud->read_single('user_id', $user_id, 'ka_vault');
		if(!empty($getvault)){
			foreach($getvault as $vault){
				$vault_type = $vault->type;
				$vault_amt = $vault->amt;
				
				if($vault_type == 'save'){
					$vault_save += $vault_amt;
				} else {
					$vault_debit += $vault_amt;
				}
			}
			
			$vault_total = $vault_save - $vault_debit;
			$empty_vault = false;
		}
		
		// get user offer savings
		$empty_offer = true;
		$offer_pending = 0;
		$offer_approve = 0;
		$offer_declined = 0;
		$alloffer = $this->Crud->read_single('user_id', $user_id, 'ka_offer');
		if(!empty($alloffer)){
			foreach($alloffer as $of){
				$interest = $of->interest;
				$status = $of->status;
				
				if($status == 'Pending') {
					$offer_pending += (float)$interest;
				} else if($status == 'Approved') {
					$offer_approve += (float)$interest;
				} else if($status == 'Declined') {
					$offer_declined += (float)$interest;
				}
			}
			$empty_offer = false;
		}
		
		// get users voluntary savings
		$empty_voluntary = true;
		$v_total = 0;
		$v_current = 0;
		$v_withdrawn = 0;
		$allvoluntary = $this->Crud->read_single('user_id', $user_id, 'ka_voluntary');
		if(!empty($allvoluntary)) {
			foreach($allvoluntary as $vol) {
				$amt = $vol->amount;
				$type = $vol->type;
				$action = $vol->action;
				$trans_status = $vol->trans_status;
				
				if(strtolower($action) == 'save'){
					if(strtolower($trans_status) == 'success'){
						$v_current += (float)$amt;
					}
				} else if(strtolower($action) == 'withdrawn') {
					$v_withdrawn += (float)$amt;
				}
			}
			$v_total = $v_current;
			$v_current = $v_current - $v_withdrawn;
			$empty_voluntary = false;
		}
		
		if($empty_vault == true && $empty_offer == true && $empty_voluntary == true) {
			// instant login notification
			$log_msg = array('text' => "8-) Your Vault/Wallet is empty in my head, you will need to Create A Savings plan or Start Contribution on created plans (Y)");
			if($log_msg){$this->_send_message($this->senderId, $log_msg);}		
		} 
		
		$vault_save = $curr.number_format((float)$vault_save,2);
		$vault_debit = $curr.number_format((float)$vault_debit,2);
		$vault_total = $curr.number_format((float)$vault_total,2);
		
		$offer_pending = $curr.number_format($offer_pending,2);
		$offer_approve = $curr.number_format($offer_approve,2);
		$offer_declined = $curr.number_format($offer_declined,2);
		
		$v_total = $curr.number_format($v_total,2);
		$v_current = $curr.number_format($v_current,2);
		$v_withdrawn = $curr.number_format($v_withdrawn,2);
		
		$vault_list = "SAVINGS VAULT \n==============\nTotal: ".$vault_save.", \nDisbursed: ".$vault_debit.", \nBalance: ".$vault_total."\n\nOFFERS VAULT \n=============\nPending: ".$offer_pending.", \nApproved: ".$offer_approve.", \nDeclined: ".$offer_declined."\n\nVOLUNTARY VAULT \n=================\nTotal: ".$v_total.", \nWithdrawn: ".$v_withdrawn.", \nBalance: ".$v_current."
		";
		
		$answer = array(
			'text' => $vault_list,
			'quick_replies' => array(
				array(
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'continue',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Create A Plan',
					'payload' => 'create a plan',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				),
				array(
					'content_type' => 'text',
					'title' => 'Savings',
					'payload' => 'all savings',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				)
			)
		 );
		return $answer;
	}
	
	/////// CREATE/EDIT DISBURSED ACCOUNT ////////////
	private function get_create_account($user_id='', $job='', $msg='', $senderId=''){
		// check loop stage
		$resp_text = '';
		$resp_btn = array();
		
		if($job == 'new') {
			// check user cache for account
			$checkcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_acc_cache');
			if(empty($checkcache)){
				// create cache for user
				$cache_data = array(
					'user_id' => $user_id,
					'status' => 1, // use this to track user stage in chat
					'stage' => 0 // use this to flag the state of flow
				);
				$this->Crud->create('ka_bot_acc_cache', $cache_data);
				$resp_text = "Great! Recieve saved funds in account. Please describe the account e.g. My Landlord, Car Dealer, etc.";
			} else {
				foreach($checkcache as $cache){
					$acc_desc = $cache->acc_desc;
					$acc_no = $cache->acc_no;
					$bank_id = $cache->bank_id;
					$acc_name = $cache->acc_name;
					$stage = $cache->stage;
					
					if($stage == 0){
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
							'stage' => 0 // use this to flag the state of flow
						);
						$resp_text = "Great! Recieve saved funds in account. Please describe the account e.g. My Landlord, Car Dealer, etc.";
					} else {
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
						);
					}
					
					// update cache status here
					$this->Crud->update('user_id', $user_id, 'ka_bot_acc_cache', $cache_data);
					
					if($acc_desc!='' && $acc_no!='' && $bank_id!=0 && $acc_name!=''){ // all completely supplied
						// get bank name
						$bank_name = '';
						$getname = $this->Crud->read_single('id', $bank_id, 'ka_bank');
						if(!empty($getname)) {
							foreach($getname as $gname){
								$bank_name = $gname->name;	
							}
						}
						
						$resp_text = "Congratulations =) You just completed Create Account walkthrough. Please review details below:\n\nAccount Description: ".ucwords($acc_desc)."\nAccount Number: ".$acc_no."\nAccount Name: ".strtoupper($acc_name)."\nBank: ".$bank_name;
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Done',
							'payload' => 'done',
							'image_url' => 'https://susu-ai.com/assets/images/green.png'
						);
					} else if($acc_desc=='') { // supply account description
						$resp_text = "Please describe the account e.g. My Landlord, Car Dealer, etc.";
					} else if($acc_no=='') { // supply account number
						$resp_text = "Please what is your ".ucwords($acc_desc)."'s account number";
					} else if($bank_id==0) { // select bank
						$resp_text = "Please select Bank";
						$count = 0;
						$getbanks = $this->Crud->read_single_order('country_id', $this->user_country, 'ka_bank', 'name', 'ASC');
						if(!empty($getbanks)){
							foreach($getbanks as $getb){
								$getb_id = $getb->id;
								$getb_name = $getb->name;
								$getb_icon = $getb->logo;
								$prev_btn = ''; // previous list button
								$next_btn = ''; // next list button
								
								if($getb_icon == ''){$getb_icon = 'assets/images/favicon.png';}
								// check more list step, just to messenger list to quickreply
								if($msg == 'list0') {
									$resp_text = "Please select your ".ucwords($acc_desc)."'s Bank (Page 1 of 4)";
									$filter = 8; // filter bank from 0-7
									if($count>=0 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $getb_name,
											'payload' => $getb_id,
											'image_url' => base_url($getb_icon)
										);
									}
									$prev_btn = '';
									$next_btn = 'list1';
								} else if($msg == 'list1') {
									$resp_text = "Please select your ".ucwords($acc_desc)."'s Bank (Page 2 of 4)";
									$filter = 16; // filter bank from 8-15
									if($count>=8 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $getb_name,
											'payload' => $getb_id,
											'image_url' => base_url($getb_icon)
										);
									}
									$prev_btn = 'list1';
									$next_btn = 'list2';
								} else if($msg == 'list2') {
									$resp_text = "Please select your ".ucwords($acc_desc)."'s Bank (Page 3 of 4)";
									$filter = 24; // filter bank from 16-23
									if($count>=16 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $getb_name,
											'payload' => $getb_id,
											'image_url' => base_url($getb_icon)
										);
									}
									$prev_btn = 'list1';
									$next_btn = 'list3';
								} else if($msg == 'list3') {
									$resp_text = "Please select your ".ucwords($acc_desc)."'s Bank (Page 4 of 4)";
									$filter = 32; // filter bank from 24-31
									if($count>=24 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $getb_name,
											'payload' => $getb_id,
											'image_url' => base_url($getb_icon)
										);
									}
									$prev_btn = 'list2';
									$next_btn = '';
								} else {
									$resp_text = "Please select your ".ucwords($acc_desc)."'s Bank (Page 1 of 4)";
									$filter = 8; // filter bank from 0-7
									if($count>=0 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $getb_name,
											'payload' => $getb_id,
											'image_url' => base_url($getb_icon)
										);
									}
									$prev_btn = '';
									$next_btn = 'list1';
								}	
								$count += 1;
							}
							
							if($prev_btn != '') { // display previous button, if not empty
								$resp_btn[] = array(
									'content_type' => 'text',
									'title' => '< Previous',
									'payload' => $prev_btn
								);
							}
							
							if($next_btn != '') { // display next button, if not empty
								$resp_btn[] = array(
									'content_type' => 'text',
									'title' => 'Next >',
									'payload' => $next_btn
								);
							}
						}
					} else if($acc_name=='') { // confirm account name
						// send notification
						$noti = array('text' => "Please wait while I validate account...");
						$this->_send_message($senderId, $noti);
						$this->_progress($senderId, 'typing_on'); // progress bar
						
						// get bank code
						$bank_code = '';
						$getcode = $this->Crud->read_single('id', $bank_id, 'ka_bank');
						if(!empty($getcode)) {
							foreach($getcode as $gcode){
								$bank_code = $gcode->code;	
							}
						}
						
						// get token and validate account
						$validate_acc_name = '';
						$gettoken = json_decode($this->Crud->pay_token());
						if($gettoken) {
							if($gettoken->status == 'success'){
								$getacc = json_decode($this->Crud->pay_validate($acc_no, $bank_code, $gettoken->token));
								if($getacc){
									if($getacc->status == 'success'){
										$acc_data = $getacc->data;
										foreach($acc_data as $key => $value){
											$validate_acc_name = $value;
										}
									}
								}
							}
						}
						
						if($validate_acc_name != '') { // validation successful
							$resp_text = "Good! - Account Validated. Is this your ".ucwords($acc_desc)."'s Accounts: ".$validate_acc_name;
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Yes',
								'payload' => $validate_acc_name,
								'image_url' => 'https://susu-ai.com/assets/images/green.png'
							);
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'No',
								'payload' => 'chg account',
								'image_url' => 'https://susu-ai.com/assets/images/red.png'
							);
						} else { // validate failed
							$resp_text = "Oops...sorry! your ".ucwords($acc_desc)."'s Accounts could not be validated, please check Account Number and/or Bank.";
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Change Account',
								'payload' => 'chg account',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Change Bank',
								'payload' => 'chg bank',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
						}
					}
				}
			}
			
			$resp_btn[] = array(
				'content_type' => 'text',
				'title' => 'Cancel',
				'payload' => 'cancel account',
				'image_url' => 'https://susu-ai.com/assets/images/red.png'
			);
		} else if($job == 'done'){
			// now clear user cache
			$savecache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_acc_cache');
			if(!empty($savecache)){
				foreach($savecache as $scache){
					$acc_desc = $scache->acc_desc;
					$acc_no = $scache->acc_no;
					$bank_id = $scache->bank_id;
					$acc_name = $scache->acc_name;
					
					// check if user already have account
					$chkmysave = $this->Crud->check3('user_id', $user_id, 'acc_no', $acc_no, 'acc_name', $acc_name, 'ka_account');
					if($chkmysave > 0){
						$resp_text = "You already have this account before";
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'Continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
						// delete the savings cache
						$this->Crud->delete('user_id', $user_id, 'ka_bot_acc_cache');
					} else {
						// now register cache data in database
						$savings = $target / $duration;
						$save_c_data = array(
							'user_id' => $user_id,
							'bank_id' => $bank_id,
							'acc_no' => $acc_no,
							'acc_name' => strtoupper($acc_name),
							'acc_desc' => ucwords($acc_desc),
						);	
						$save_p_id = $this->Crud->create('ka_account', $save_c_data);
						if($save_p_id){
							// delete the savings cache
							$this->Crud->delete('user_id', $user_id, 'ka_bot_acc_cache');
							$resp_text = "Great! (y) - Your Disbursed Account created successfully.";
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Disbursement Accounts',
								'payload' => 'disbursement accounts',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Link Savings',
								'payload' => 'link',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Continue',
								'payload' => 'Continue',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
						}
					}
				}
			}
		} else if($job == 'cancel'){
			// now clear user cache
			$clearcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_acc_cache');
			if(!empty($clearcache)){
				$this->Crud->delete('user_id', $user_id, 'ka_bot_acc_cache');
				$resp_text = "Oops! - Your Disbursed Account walkthrough is cleared";
				$resp_btn[] = array(
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'Continue',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				);
			}
		}
		
		$answer = array(
			'text' => $resp_text,
			'quick_replies' => $resp_btn
		);
		return $answer;
	}
	
	/////// ALL DISBURSEMENT ACCOUNTS ////////////
	private function get_all_accounts($user_id=''){
		// get user disbursement accounts
		$acc_list = array();
		$getacc = $this->Crud->read_single('user_id', $user_id, 'ka_account');
		if(!empty($getacc)){
			foreach($getacc as $acc){
				$acc_id = $acc->id;
				$acc_desc = $acc->acc_desc;
				
				$acc_list[] = array(
					'content_type' => 'text',
					'title' => ucwords($acc_desc),
					'payload' => 'DA '.ucwords($acc_desc),
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				);
			}
		}
		
		if(!empty($acc_list)){
			$acc_list[] = array(
				'content_type' => 'text',
				'title' => 'Create Account',
				'payload' => 'Create Account',
				'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
			);
			
			$answer = array(
				'text' => 'Which of the Disbursement Account would you like to check?',
				'quick_replies' => $acc_list
			 );	
		} else {
			$answer = array(
				'text' => "B-) Your Disbursement Account is empty in my head, you will need to create one (Y)",
				'quick_replies' => array(
					array(
						'content_type' => 'text',
						'title' => 'Create Account',
						'payload' => 'Create Account',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					)
				)
			);
		}
		return $answer;
	}
	
	/////// EACH DISBURSED ACCOUNT ////////////
	private function get_each_account($user_id='', $acc=''){
		// get user account
		$link_saving_btn = array();
		$link_saving_count = 0;
		$acc_text = 'Oops! - I can not find details about this disbursement account';
		
		// get actual account name
		$acc = str_replace('da ', '', $acc); // remove da prefix
		$getacc = $this->Crud->read2('user_id', $user_id, 'acc_desc', $acc, 'ka_account');
		if(!empty($getacc)){
			foreach($getacc as $acc){
				$acc_id = $acc->id;
				$bank_id = $acc->bank_id;
				$acc_no = $acc->acc_no;
				$acc_name = $acc->acc_name;
				$acc_desc = $acc->acc_desc;
				
				// get bank name
				$bank_name = '';
				$getname = $this->Crud->read_single('id', $bank_id, 'ka_bank');
				if(!empty($getname)) {
					foreach($getname as $gname){
						$bank_name = $gname->name;	
					}
				}
				
				// get linked savings
				$getlinked = $this->Crud->read2('user_id', $user_id, 'acc_id', $acc_id, 'ka_account_link');
				if(!empty($getlinked)){
					foreach($getlinked as $glink) {
						// get savings in list
						$getsave = $this->Crud->read_single('id', $glink->saving_id, 'ka_personal');
						if(!empty($getsave)){
							foreach($getsave as $save) {
								$saving_name = ucwords($save->name);
								$link_saving_btn[] = array(
									'content_type' => 'text',
									'title' => $saving_name,
									'payload' => $saving_name,
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								);
								$link_saving_count += 1; // count all linked savings
							}
						}
					}
				}
				
				if($link_saving_count > 0){
					$link_saving_info = 'found, see list below';	
				} else {
					$link_saving_info = '';	
				}
				
				
				$acc_text = ucwords($acc_desc)." Details:\n\nAccount Number: ".$acc_no."\nAccount Name: ".$acc_name."\nBank: ".$bank_name."\nLinked Savings: ".$link_saving_count." ".$link_saving_info;
			}
		}
		
		$link_saving_btn[] = array(
			'content_type' => 'text',
			'title' => 'Link Savings',
			'payload' => 'link',
			'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
		);
		
		$link_saving_btn[] = array(
			'content_type' => 'text',
			'title' => 'Continue',
			'payload' => 'continue',
			'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
		);
		
		$answer = array(
			'text' => $acc_text,
			'quick_replies' => $link_saving_btn
		);		
		return $answer;
	}
	
	/////// LINK SAVINGS TO ACCOUNT ////////////
	private function get_link_savings_to_account($user_id='', $senderId='', $msg=''){
		// check the link intent stage
		$answer = '';
		$link_phrase = '';
		$link_plan = '';
		$link_account = '';
		$msg_intent = explode(' ', trim($msg)); // break link into phrases
		if(count($msg_intent) <= 1 || empty($msg_intent)) { // means only link is in intent
			$answer = array(
				'text' => ":O Awwz...you need to tell me what PLAN to link to ACCOUNT. It must be this format \n\n'Link [Plan Name] to [Account Name]', e.g. Link Buy Phone to Phone Seller. \n\n[Account Name] can also be your personal account, just your choice. Completed savings will be disbursed to that account.",
				'quick_replies' => array(
					array(
						'content_type' => 'text',
						'title' => 'Savings',
						'payload' => 'all savings',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					),
					array(
						'content_type' => 'text',
						'title' => 'Continue',
						'payload' => 'continue',
						'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
					)
				)
			);
		} else {
			// first get the Plan and Account and remove Link and To
			for($i=1; $i<count($msg_intent); $i++) {
				// don't add space after last item
				if($i == count($msg_intent)) {
					$link_phrase .= $msg_intent[$i];
				} else {
					$link_phrase .= $msg_intent[$i].' ';
				}
			}
			
			$link_phrase = trim($link_phrase);
			
			// now check is link format is in right phrase
			$phrase = explode('to', $link_phrase);
			if(count($phrase) < 2) { // means phrase is not in right format
				$answer = array(
					'text' => ":O Oops!...you are not still getting it right. It must be this format \n\n'Link [Plan Name] to [Account Name]', e.g. Link Buy Phone to Phone Seller.",
					'quick_replies' => array(
						array(
							'content_type' => 'text',
							'title' => 'Savings',
							'payload' => 'all savings',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						),
						array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						)
					)
				);
			} else {
				// now check if confirmation is added
				if(strpos($link_phrase, ':~') !== false) {
					$link_array = explode(':~', $link_phrase);
					$link_item = $link_array[0];
					$link_resp = $link_array[1];
					$resp_text = '';
					$resp_btn = array();
					
					if($link_resp == 'no') {
						$resp_text = ":) OK! - I've cleared the linking process.";
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
					} else {
						// now break item by to
						$break_item = explode('to', trim($link_item));
						$break_plan = trim($break_item[0]);
						$break_acc = trim($break_item[1]);
						$plan_id = 0;
						$acc_id = 0;
						$acc_name = '';
						$acc_no = '';
						$acc_bank = '';
						
						// check if saving plan
						$check_plan = $this->Crud->read2('user_id', $user_id, 'name', $break_plan, 'ka_personal');
						if(empty($check_plan)){ // plan not found, send create a plan button
							$resp_text = ":) Awwz! - I could not find ".ucwords($break_plan)." in your savings plan list.";
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Savings Plan',
								'payload' => 'all savings',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
						} else {
							foreach($check_plan as $cplan){
								$plan_id = $cplan->id;	
							}
						}
						
						// check if account
						$check_acc = $this->Crud->read2('user_id', $user_id, 'acc_desc', $break_acc, 'ka_account');
						if(empty($check_acc)){ // account not found, send account button
							$resp_text .= ":) Awwz! - I could not find ".ucwords($break_acc)." in your accounts list.";
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Accounts',
								'payload' => 'accounts',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
						} else {
							foreach($check_acc as $cacc){
								$acc_id = $cacc->id;
								$acc_name = $cacc->acc_name;
								$acc_no = $cacc->acc_no;
								
								// get bank name
								$getbank = $this->Crud->read_single('id', $cacc->bank_id, 'ka_bank');
								if(!empty($getbank)){
									foreach($getbank as $bank){
										$acc_bank = $bank->name;
									}
								}
							}
						}
						
						// check if already linked
						if($plan_id!=0 && $acc_id!=0) {
							$is_link = $this->Crud->read3('user_id', $user_id, 'saving_id', $plan_id, 'acc_id', $acc_id, 'ka_account_link');
							if(!empty($is_link)){ // inform that plan is already linked to account
								$resp_text = ":) Nice! - ".ucwords($break_plan)." already linked to ".ucwords($break_acc).". See details below:\n\nAccount Name: ".$acc_name."\nAccount Number: ".$acc_no."\nBank: ".$acc_bank;
							} else { // insert and link account
								$now = date('Y-m-d H:i:s');
								$save_link = array(
									'user_id' => $user_id,
									'saving_id' => $plan_id,
									'acc_id' => $acc_id,
									'reg_date' => $now
								);
								if($this->Crud->create('ka_account_link', $save_link) > 0){ 
									$resp_text = ":) Great! - I've linked ".ucwords($break_plan)." to ".ucwords($break_acc).". See details below:\n\nAccount Name: ".$acc_name."\nAccount Number: ".$acc_no."\nBank: ".$acc_bank;
								} else {
									$resp_text = ":) Sorry! - Please try later or check your connection.";	
								}
								
							}
						}
						
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
					}
					
					$answer = array(
						'text' => $resp_text,
						'quick_replies' => $resp_btn
					);
				} else {
					// prepare confirmation
					$payload_yes = 'Link '.$link_phrase.':~yes';
					$payload_no = 'Link '.$link_phrase.':~no';
					$answer = array(
						'text' => ":O Looks Great!...just to be sure we are on same page. You are about to link the following:\n\nSAVINGS PLAN: ".ucwords($phrase[0])."\nACCOUNT: ".ucwords($phrase[1]),
						'quick_replies' => array(
							array(
								'content_type' => 'text',
								'title' => 'Yes',
								'payload' => $payload_yes,
								'image_url' => 'https://susu-ai.com/assets/images/green.png'
							),
							array(
								'content_type' => 'text',
								'title' => 'No',
								'payload' => $payload_no,
								'image_url' => 'https://susu-ai.com/assets/images/red.png'
							)
						)
					);
				}
			}
		}
		return $answer;
	}
	
	/////// CREATE/EDIT OFFER ////////////
	private function get_create_offer($user_id='', $job='', $msg='', $senderId=''){
		// check loop stage
		$my_curr = $this->user_currency;
		$resp_text = '';
		$resp_btn = array();
		
		if($job == 'new') {
			// check user cache for offer
			$checkcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_offer_cache');
			if(empty($checkcache)){
				// create cache for user
				$cache_data = array(
					'user_id' => $user_id,
					'status' => 1, // use this to track user stage in chat
					'stage' => 0 // use this to flag the state of flow
				);
				$this->Crud->create('ka_bot_offer_cache', $cache_data);
				$resp_text = "Please select your offer";
				// get all offers in user country
				$getpartner = $this->Crud->read_single('country_id', $this->user_country, 'ka_offer_partner');
				if(!empty($getpartner)){
					$resp_text = "Subscribe to an offer, click 'Subscribe Now' to get started";
					$offer_element = array();
					foreach($getpartner as $partner) {
						$offer_element[] = array(
							'title' => $partner->name.' Offer',
							'item_url' => '',
							'image_url' => 'https://susu-ai.com/'.$partner->img,
							'subtitle' => 'Complete Savings, purchase on '.$partner->name.', SusuAI pay you upto 7% within 30days',
							'buttons' => array(
								array(
									'type' => 'web_url',
									'url' => 'https://susu-ai.com/offers/jumia',
									'title' => 'Details',
									'webview_height_ratio' => 'tall'
								),
								array(
									'type' => 'element_share',
									'share_contents' => array(
										'attachment' => array(
											'type' => 'template',
											'payload' => array(
												'template_type' => 'generic',
												'elements' => array(
													array(
														'title' => $partner->name.' Offer',
														'subtitle' => 'Complete Savings, purchase on '.$partner->name.', SusuAI pay you upto 7% within 30days',
														'image_url' => 'https://susu-ai.com/'.$partner->img,
														'default_action' => array (
															'type' => 'web_url',
															'url' => 'https://m.me/susuaibot?ref=offers',
														),
														'buttons' => array(
															array(
																'type' => 'web_url',
																'url' => 'https://m.me/susuaibot?ref=offers',
																'title' => 'Subscribe Now'
															)
														)
													)
												)
											)
										)
									 )
								),
								array(
									'type' => 'postback',
									'title' => 'Subscribe Now',
									'payload' => $partner->id
								)
							)
						);
					}
					
					$offer_answer = array(
						'attachment' => array(
							'type' => 'template',
							'payload' => array(
								'template_type' => 'generic',
								'elements' => $offer_element
							)
						)
					 );	
					 $this->_send_message($this->senderId, $offer_answer);
				} else {
					$resp_text = "Oops! So sorry, no offer for your country yet. I will notify you once I have. Thank you";
				}
			} else {
				foreach($checkcache as $cache){
					$partner_id = $cache->partner_id;
					$savings_id = $cache->savings_id;
					$com_id = $cache->com_id;
					$product_link = $cache->product_link;
					$stage = $cache->stage;
					
					if($stage == 0){
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
							'stage' => 0 // use this to flag the state of flow
						);
						$resp_text = "Please select your offer";
					} else {
						$cache_data = array(
							'status' => 1, // use this to track user stage in chat
						);
					}
					
					// update cache status here
					$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $cache_data);
					
					if($partner_id!=0 && $savings_id!=0 && $com_id!=0 && $product_link!=''){ // all completely supplied
						// get partner name
						$partner_name = '';
						$getpname = $this->Crud->read_single('id', $partner_id, 'ka_offer_partner');
						if(!empty($getpname)) {
							foreach($getpname as $pname){
								$partner_name = $pname->name;	
							}
						}
						
						// get savings
						$saving_name = '';
						$saving_target = 0;
						$getsave = $this->Crud->read_single('id', $savings_id, 'ka_personal');
						if(!empty($getsave)) {
							foreach($getsave as $sav){
								$saving_name = $sav->name;
								$saving_target = (float)$sav->target;	
							}
						}
						
						// get commission
						$comm_name = '';
						$comm = '';
						$getccom = $this->Crud->read_single('id', $com_id, 'ka_offer_commission');
						if(!empty($getccom)) {
							foreach($getccom as $ccom){
								$comm_name = $ccom->name;	
								$comm = (float)$ccom->com;	
							}
						}
						
						$money_back = $saving_target * ($comm / 100);
						
						$resp_text = "Congratulations =) You just completed ".$partner_name." Offer walkthrough. Please review details below:\n\nSavings Plan: ".ucwords($saving_name)."\nProduct Category: ".$comm_name."\nProduct Link: ".$product_link."\nMoney Back (Within 30days): ".$my_curr.number_format($money_back,2)." (".$comm."%)";
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Done',
							'payload' => 'done',
							'image_url' => 'https://susu-ai.com/assets/images/green.png'
						);
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Change Category',
							'payload' => 'chgo category',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Change Product Link',
							'payload' => 'chgo product',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
					} else if($partner_id==0) { // select partner
						// ensure loop consistency
						$off_loop = array('stage'=>0);
						$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $off_loop);
						
						// get all offers in user country
						$getpartner = $this->Crud->read_single('country_id', $this->user_country, 'ka_offer_partner');
						if(!empty($getpartner)){
							$resp_text = "Please select an offer";
							foreach($getpartner as $partner) {
								$resp_btn[] = array(
									'content_type' => 'text',
									'title' => $partner->name,
									'payload' => $partner->id,
									'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
								);	
							}
						} else {
							$resp_text = "Oops! So sorry, no offer for your country yet. I will notify you once I have. Thank you";
						}
					} else if($savings_id==0) { // select saving plan
						// ensure loop consistency
						$off_loop = array('stage'=>1);
						$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $off_loop);
						
						// get all user savings plan
						$getsaving = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
						if(!empty($getsaving)) {
							$resp_text = "Please which of your Savings Plan do you want to use to purchase this Offer?\n\nPLEASE NOTE: Savings Plan must have same price as the product you wanted to purchase, because money back is only based on Total amount of purchased product.";
							foreach($getsaving as $saving){
								if($saving->complete == 0) { // completed savings can enroll
									$resp_btn[] = array(
										'content_type' => 'text',
										'title' => ucwords($saving->name),
										'payload' => $saving->id,
										'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
									);
								}
							}
						} else {
							$resp_text = "Oops! Sorry, I can't find Savings Plan in your list, you can create one and come back to offer. Hmmmm, don't worry I will continue from where you stopped, you just type 'Offers' when you are done and I will continue.";
						}
						// add create a plan button
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Create A Plan',
							'payload' => 'create a plan',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
					} else if($com_id==0) { // select commission
						// ensure loop consistency
						$off_loop = array('stage'=>2);
						$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $off_loop);
						
						// get all categories
						$resp_text = "Great, which category of Product savings plan belongs to?";
						$count = 0;
						$getcomm = $this->Crud->read_single_order('partner_id', $partner_id, 'ka_offer_commission', 'name', 'ASC');
						if(!empty($getcomm)){
							foreach($getcomm as $comm){
								$comm_id = $comm->id;
								$comm_name = $comm->name;
								$prev_btn = ''; // previous list button
								$next_btn = ''; // next list button
								
								$getc_icon = 'assets/images/favicon.png';
								// check more list step, just to messenger list to quickreply
								if($msg == 'list0') {
									$resp_text = "Please select your Product Category, if you not sure, select 'Others' from list (Page 1 of 2)";
									$filter = 8; // filter bank from 0-7
									if($count>=0 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $comm_name,
											'payload' => $comm_id,
											'image_url' => base_url($getc_icon)
										);
									}
									$prev_btn = '';
									$next_btn = 'list1';
								} else if($msg == 'list1') {
									$resp_text = "Please select your Product Category, if you not sure, select 'Others' from list (Page 2 of 2)";
									$filter = 16; // filter bank from 8-15
									if($count>=8 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $comm_name,
											'payload' => $comm_id,
											'image_url' => base_url($getc_icon)
										);
									}
									$prev_btn = 'list0';
									$next_btn = '';
								} else {
									$resp_text = "Please select your Product Category (Page 1 of 2)";
									$filter = 8; // filter bank from 0-7
									if($count>=0 && $count<$filter) {
										$resp_btn[] = array(
											'content_type' => 'text',
											'title' => $comm_name,
											'payload' => $comm_id,
											'image_url' => base_url($getc_icon)
										);
									}
									$prev_btn = '';
									$next_btn = 'list1';
								}	
								$count += 1;
							}
							
							if($prev_btn != '') { // display previous button, if not empty
								$resp_btn[] = array(
									'content_type' => 'text',
									'title' => '< Previous',
									'payload' => $prev_btn
								);
							}
							
							if($next_btn != '') { // display next button, if not empty
								$resp_btn[] = array(
									'content_type' => 'text',
									'title' => 'Next >',
									'payload' => $next_btn
								);
							}
						}
					} else if($product_link=='') { // supply product link
						// ensure loop consistency
						$off_loop = array('stage'=>3);
						$this->Crud->update('user_id', $user_id, 'ka_bot_offer_cache', $off_loop);
						
						// send sample
						$sample_answer = array(
							'attachment' => array(
								'type' => 'template',
								'payload' => array(
									'template_type' => 'generic',
									'elements' => array(
										array(
											'title' => 'How To Get Link',
											'item_url' => '',
											'image_url' => 'https://susu-ai.com/assets/images/jumia_link.jpg',
											'subtitle' => 'Go to Jumia website, copy the link to your product and paste it to me',
											'buttons' => array(
												array(
													'type' => 'web_url',
													'url' => 'https://susu-ai.com/assets/images/jumia_sample.jpg',
													'title' => 'View Sample',
													'webview_height_ratio' => 'tall'
												)
											)
										)	
									)
								)
							)
						 );	
						 $this->_send_message($this->senderId, $sample_answer); 
						$resp_text = "Lastly, now go to Jumia website, copy the link to your product and paste it to me. Click 'View Sample' to see how";
					}
				}
			}
			
			$resp_btn[] = array(
				'content_type' => 'text',
				'title' => 'Cancel',
				'payload' => 'cancel offe',
				'image_url' => 'https://susu-ai.com/assets/images/red.png'
			);
		} else if($job == 'done'){
			// now clear user cache
			$savecache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_offer_cache');
			if(!empty($savecache)){
				foreach($savecache as $scache){
					$partner_id = $scache->partner_id;
					$savings_id = $scache->savings_id;
					$com_id = $scache->com_id;
					$product_link = $scache->product_link;
					$stage = $scache->stage;
					
					// check if user already have offer linked to savings
					$chkmysave = $this->Crud->check2('user_id', $user_id, 'saving_id', $savings_id, 'ka_offer');
					if($chkmysave > 0){
						$resp_text = "Savings Plan already subscribed to an Offer";
						$resp_btn[] = array(
							'content_type' => 'text',
							'title' => 'Continue',
							'payload' => 'Continue',
							'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
						);
						// delete the savings cache
						$this->Crud->delete('user_id', $user_id, 'ka_bot_offer_cache');
					} else {
						// now register cache data in database
						// get savings
						$saving_name = '';
						$saving_target = 0;
						$getsave = $this->Crud->read_single('id', $savings_id, 'ka_personal');
						if(!empty($getsave)) {
							foreach($getsave as $sav){
								$saving_name = $sav->name;
								$saving_target = (float)$sav->target;	
							}
						}
						
						// get commission
						$comm_name = '';
						$comm = '';
						$getccom = $this->Crud->read_single('id', $com_id, 'ka_offer_commission');
						if(!empty($getccom)) {
							foreach($getccom as $ccom){
								$comm_name = $ccom->name;	
								$comm = (float)$ccom->com;	
							}
						}
						
						$interest = $saving_target * ($comm / 100);
						$offer_no = rand();
						$offer_link = 'http://c.jumia.io/?a=31860&c=11&p=r&E=kkYNyk2M4sk%3d&ckmrdr='.$product_link.'&s1='.$offer_no.'&utm_source=cake&utm_medium=affiliation&utm_campaign=31860&utm_term='.$offer_no;
						$status = 'Pending';
						$now = date(fdate);
						
						$save_c_data = array(
							'user_id' => $user_id, 
							'com_id' => $com_id, 
							'saving_id' => $savings_id, 
							'interest' => $interest, 
							'offer_no' => $offer_no, 
							'product_link' => $product_link, 
							'offer_link' => $offer_link, 
							'status' => $status, 
							'reg_date' => $now
						);	
						$save_p_id = $this->Crud->create('ka_offer', $save_c_data);
						if($save_p_id){
							// delete the savings cache
							$this->Crud->delete('user_id', $user_id, 'ka_bot_offer_cache');
							$resp_text = "Great! (y) - Your Offer is linked to (".ucwords($saving_name)."), once the savings is completed and you comply with instruction to purchase your product, SusuAI will pay you back ".$my_curr.number_format($interest, 2)." (".$comm."%) within 30 days";
							
							$resp_btn[] = array(
								'content_type' => 'text',
								'title' => 'Continue',
								'payload' => 'Continue',
								'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
							);
						}
					}
				}
			}
		} else if($job == 'cancel'){
			// now clear user cache
			$clearcache = $this->Crud->read_single('user_id', $user_id, 'ka_bot_offer_cache');
			if(!empty($clearcache)){
				$this->Crud->delete('user_id', $user_id, 'ka_bot_offer_cache');
				$resp_text = "Oops! - Your Offer walkthrough is cleared";
				$resp_btn[] = array(
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'Continue',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				);
			}
		}
		
		$answer = array(
			'text' => $resp_text,
			'quick_replies' => $resp_btn
		);
		return $answer;
	}
	
	/////// LOGIN STATUS ////////////
	private function get_login_status($user_othername='', $user_lastname='', $user_email='', $user_phone=''){
		$answer = array(
			'text' => "<3 You are logged in. Details on ".app_name.": \n\nName: ".$user_othername." ".$user_lastname.", \nEmail: ".$user_email.", \nPhone: ".$user_phone.". (y) \n\nType 'Logout' to signout from your SusuAI account here.",
			'quick_replies' => array(
				array(
					'content_type' => 'text',
					'title' => 'Continue',
					'payload' => 'get started',
					'image_url' => 'https://susu-ai.com/assets/images/favicon.png'
				)
			)
		 );			
		return $answer;
	}
	
	/////// REQUEST AUTH ////////////
	private function request_auth($push='', $sender=''){
		$auth_param = 'ref=fbbot&push='.$push.'&sender='.$sender;
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'generic',
					'elements' => array(
						array(
							'title' => 'Please login to access Vault/Savings or Contribute',
							'item_url' => 'https://susu-ai.com/login',
							'image_url' => 'https://susu-ai.com/assets/images/lock.png',
							'subtitle' => 'I\'ll use it to protect your data',
							'buttons' => array(
								array(
									'type' => 'account_link',
									'url' => 'https://susu-ai.com/login?'.$auth_param
								)
							)
						)
					)
				)
			)
		 );	
		 return $answer;
	}
	
	/////// DELETE AUTH ////////////
	private function remove_auth($push='', $sender=''){
		$auth_param = 'ref=fbbot&push='.$push.'&sender='.$sender;
		$answer = array(
			'attachment' => array(
				'type' => 'template',
				'payload' => array(
					'template_type' => 'generic',
					'elements' => array(
						array(
							'title' => 'You will be logged out from SusuAI',
							'item_url' => 'https://susu-ai.com/logout',
							'image_url' => 'https://susu-ai.com/assets/images/unlock.png',
							'subtitle' => 'I\'ll clear your data off my head',
							'buttons' => array(
								array(
									'type' => 'account_unlink'
								),
								array(
									'type' => 'postback',
									'title' => 'Continue',
									'payload' => 'Continue'
								)
							)
						)
					)
				)
			)
		 );		
		 return $answer;
	}
	
	/////// SEND SAVING REMINDER NOTIFICATION ////////////
	public function send_reminder_notify(){
		// get all personal
		$allpers = $this->Crud->read('ka_personal');
		if(!empty($allpers)){
			foreach($allpers as $pers){
				$pa_id = $pers->id;
				$pa_user_id = $pers->user_id;
				$pa_name = $pers->name;
				$pa_target = $pers->target;
				$pa_duration = $pers->duration;
				$pa_cycle = $pers->cycle;
				$pa_expired = $pers->expired;
				$pa_savings = $pers->saving;
				$pa_next = $pers->saving_next;
				
				if($pa_expired == 0) {
					// for only active savings
					if($pa_cycle > 0) {
						// check if contribution made already
						$pa_today = strtotime(date('Y-m-d'));
						$pa_current = strtotime(date('Y-m-d', strtotime($pa_next)));
						$date_diff = $pa_today - $pa_current;
						$date_diff = floor($date_diff / (60 * 60 * 24));
						
						if($date_diff >= 0) {
							// get the user psid
							$getupsid = $this->Crud->read_single('id', $pa_user_id, 'ka_user');
							if(!empty($getupsid)) {
								foreach($getupsid as $upsid) {
									$pa_user_psid = $upsid->fbbot_psid;
									$pa_user_country = $upsid->country;
									
									// get currency
									$pa_user_curr = '₦'; // set default if user don't have currency
									$getct = $this->Crud->read_single('id', $pa_user_country, 'ka_country');
									if(!empty($getct)){
										foreach($getct as $ct){
											$pa_user_curr = $ct->currency;
											if($pa_user_curr == 'N'){$pa_user_curr = '₦';}
										}
									}
									
									if($pa_user_psid != '') {
										$answer = array(
											'attachment' => array(
												'type' => 'template',
												'payload' => array(
													'template_type' => 'generic',
													'elements' => array(
														array(
															'title' => 'Contribution Reminder',
															'item_url' => 'https://susu-ai.com/savings/personal/p/'.$pa_id.'?sender='.$pa_user_psid,
															'image_url' => 'https://susu-ai.com/landing/img/iphone_img.png',
															'subtitle' => 'Your next contribution cycle ('.$pa_cycle.' of '.$pa_duration.') cycle of your '.ucwords($pa_name).' savings - '.$pa_user_curr.number_format((float)$pa_savings,2),
															'buttons' => array(
																array(
																	'type' => 'web_url',
																	'url' => 'https://susu-ai.com/savings/personal/p/'.$pa_id.'?sender='.$pa_user_psid,
																	'title' => 'Approve Contribution',
																	'webview_height_ratio' => 'tall'
																)
															)
														)
													)
												)
											)
										 );
										 
										 // send bot
										 $accessToken = $this->accessTokens;
										 $response = array(
											'recipient' => array( 'id' => $pa_user_psid ),
											'message' => $answer
										);
										
										$this->Crud_messenger->message($accessToken, $response);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	/////// SEND INACTIVE SAVINGS REMINDER NOTIFICATION ////////////
	public function send_inactive_reminder_notify(){
		// get all personal
		$allpers = $this->Crud->read_single('active', 0, 'ka_personal');;
		if(!empty($allpers)){
			foreach($allpers as $pers){
				$pa_id = $pers->id;
				$pa_user_id = $pers->user_id;
				$pa_name = $pers->name;
				$pa_target = $pers->target;
				$pa_duration = $pers->duration;
				$pa_cycle = $pers->cycle;
				$pa_expired = $pers->expired;
				$pa_savings = $pers->saving;
				$pa_saving_start = $pers->saving_start;
				
				if($pa_saving_start == '') {
					// get the user psid
					$getupsid = $this->Crud->read_single('id', $pa_user_id, 'ka_user');
					if(!empty($getupsid)) {
						foreach($getupsid as $upsid) {
							$pa_user_psid = $upsid->fbbot_psid;
							if($pa_user_psid != '') {
								$answer = array(
									'attachment' => array(
										'type' => 'template',
										'payload' => array(
											'template_type' => 'generic',
											'elements' => array(
												array(
													'title' => ucwords($pa_name).' Reminder',
													'item_url' => 'https://susu-ai.com/savings/personal/p/'.$pa_id.'?sender='.$pa_user_psid,
													'image_url' => 'https://susu-ai.com/landing/img/iphone_img.png',
													'subtitle' => 'You are yet to start your '.ucwords($pa_name).' savings. Why not start your contribution today and let SusuAI automate it for you',
													'buttons' => array(
														array(
															'type' => 'web_url',
															'url' => 'https://susu-ai.com/savings/personal/p/'.$pa_id.'?sender='.$pa_user_psid,
															'title' => 'Start Contribution',
															'webview_height_ratio' => 'tall'
														),
														array(
															'type' => 'postback',
															'title' => 'Delete Plan',
															'payload' => 'Delete '.ucwords($pa_name)
														)
													)
												)
											)
										)
									)
								 );
								 
								 // send bot
								 $accessToken = $this->accessToken;;
								 $response = array(
									'recipient' => array( 'id' => $pa_user_psid ),
									'message' => $answer
								);
								
								$this->Crud_messenger->message($accessToken, $response);
							}
						}
					}
				}
			}
		}
	}
	
	/////// CURRENCY ////////////
	private function _currency($country=''){
		$getcurrency = '₦';
		
		if($country == 'Nigeria') {
			$getcurrency = '₦';
		} else if($country == 'Ghana') {
			$getcurrency = 'GH¢';
		} else if($country == 'Kenya') {
			$getcurrency = 'KSh';
		}
		
		return $getcurrency;
	}
	
	/////////// BROADCAST //////
	public function broadcast() {
		$sent_count = 0;
		
		$data['result'] = $sent_count;	
		$this->load->view('bots/messenger', $data);
	}
	
	/////////// SUBSCRIBE APP //////
	public function subscribe_app() {
		$data['result'] = $this->Crud_messenger->subscribe($this->accessToken);	
		$this->load->view('bots/messenger', $data);
	}
	
	/////////// PERSISTENT MENU //////
	public function persistent() {
		$persistance_menu = array (
			'setting_type' => 'call_to_actions',
			'thread_state' => 'existing_thread',
			'call_to_actions' => array (
				0 => array (
					'type' => 'postback',
					'title' => 'Offers',
					'payload' => 'offer'
				),
				1 => array (
					'type' => 'postback',
					'title' => 'Create A Plan',
					'payload' => 'create plan'
				),
				2 => array (
					'type' => 'postback',
					'title' => 'Savings Status',
					'payload' => 'savings'
				),
				3 => array (
					'type' => 'postback',
					'title' => 'Check My Vault',
					'payload' => 'check my vault'
				),
				4 => array (
					'type' => 'postback',
					'title' => 'Bot Keywords',
					'payload' => 'keywords'
				)
			),
		);
		$data['result'] = $this->Crud_messenger->settings($this->accessToken, $persistance_menu);	
		$this->load->view('bots/messenger', $data);
	}
	
	public function delete_persistent() {
		$persistance_menu = array (
			'setting_type' => 'call_to_actions',
			'thread_state' => 'existing_thread'
		);
		$data['result'] = $this->Crud_messenger->remove_settings($this->accessToken, $persistance_menu);	
		$this->load->view('bots/messenger', $data);
	}
	
	/////////// GET STARTED BUTTON //////
	public function gstart() {
		$gstart_menu = array (
			'setting_type' => 'call_to_actions',
			'thread_state' => 'new_thread',
			'call_to_actions' => array (
				0 => array (
					'payload' => 'welcome'
				)
			),
		);
		$data['result'] = $this->Crud_messenger->settings($this->accessToken, $gstart_menu);	
		$this->load->view('bots/messenger', $data);
	}
	
	public function delete_gstart() {
		$gstart_menu = array (
			'setting_type' => 'call_to_actions',
			'thread_state' => 'new_thread'
		);
		$data['result'] = $this->Crud_messenger->remove_settings($this->accessToken, $gstart_menu);	
		$this->load->view('bots/messenger', $data);
	}
	
	/////////// WHITELIST DOMAIN //////
	public function whitelist() {
		$gwhitelist = array (
			'setting_type' => 'domain_whitelisting',
			'whitelisted_domains' => array (
				0 => 'https://susu-ai.com'
			),
			'domain_action_type' => 'add',
		);
		$data['result'] = $this->Crud_messenger->settings($this->accessToken, $gwhitelist);	
		$this->load->view('bots/messenger', $data);
	}
	
	public function delete_whitelist() {
		//$gwhitelist = array (
//			'fields' => array (
//				0 => 'whitelisted_domains'
//			),
//		);
//		$this->Crud_messenger->remove_settings($this->accessToken, $gwhitelist);	
	}
}