<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Savings extends CI_Controller {

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
		redirect(base_url('savings/personal'), 'refresh');
	}
	
	///////////// ************ PERSONAL SAVINGS ************* /////////////////////
	public function personal($param1='', $param2='') {
		$data['my_curr'] = $this->country_curr; // pass to views
		// check if user is from bot
		$ref_sender = $this->input->get('sender');
		$ref_save = array('ka_ref_sender' => $ref_sender);
		$this->session->set_userdata($ref_save);
		
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
		}
		$now = date(fdate);
		$data['reset'] = FALSE;
		$data['done_savings'] = FALSE;
		$data['u_othername'] = $user_othername;
		$data['u_lastname'] = $user_lastname;
		
		//////////////// CREATE //////////////////////////////////////////
		if($param1 == 'add'){
			if($_POST) {
				if(isset($_POST['btnCountry'])){
					$country_id = $_POST['country_id'];
					
					if($country_id == '') {
						$data['err_msg'] = $this->Crud->msg('warning', 'You have not selected Country!');
					} else {
						$updc_data = array('country' => $country_id);
						$upd_ct = $this->Crud->update('id', $user_id, 'ka_user', $updc_data);
						if($upd_ct > 0) {
							$country_name = $this->Crud->country_data($country_id, 'name');
							$country_code = $this->Crud->country_data($country_id, 'code');
							$country_currency = $this->Crud->country_data($country_id, 'currency');
							// update session
							$sc_data = array(
								'kas_user_country_id' => $country_id,
								'kas_user_country_name' => $country_name,
								'kas_user_country_code' => $country_code,
								'kas_user_country_currency' => $country_currency
							);	
							$this->session->set_userdata($sc_data);
							redirect(base_url('savings/personal/add'), 'refresh');
						}
					}
				} else if(isset($_POST['btnSavings'])){
					$name = $_POST['name'];	
					$target = $_POST['target'];	
					$contribute = $_POST['contribute'];	
					$contribute_amt = $_POST['contribute_amt'];	
					$duration = $_POST['duration'];	
					
					if($this->Crud->check2('user_id', $user_id, 'name', $name, 'ka_personal') > 0){
						$data['err_msg'] = $this->Crud->msg('warning', 'You already have a savings named <b>'.$name.'</b>');
					} else {
						// register savings
						$sav_data = array(
							'user_id' => $user_id,
							'name' => trim($name),
							'target' => $target,
							'type' => $contribute,
							'duration' => $duration,
							'saving' => $contribute_amt,
							'reg_date' => $now
						);
						$s_ins_id = $this->Crud->create('ka_personal', $sav_data);
						if($s_ins_id){
							$data['err_msg'] = $this->Crud->msg('success', 'Great! - Now time to assign account to push funds when savings is completed');
							if($contribute == 'Monthly'){
								$type = 'Month';	
							} else if($contribute == 'Weekly'){
								$type = 'Week';	
							} else {
								$type = 'Day';
							}
							
							if($duration > 1){$type.='s';}
						
							$data['p_save_id'] = $s_ins_id;
							$data['get_name'] = ucwords($name);
							$data['get_target'] = $this->country_curr.number_format($target,2);
							$data['get_duration'] = $duration.' '.$type;
							$data['get_contibute'] = $this->country_curr.number_format((float)$contribute_amt,2).' '.$contribute;
							$data['done_savings'] = TRUE;
							
							// prepare push notification
							$n_user_id = $user_id; 
							$n_othername = $this->session->userdata('kas_user_othername'); 
							$n_email = $this->session->userdata('kas_user_email'); 
							$n_phone = $this->session->userdata('kas_user_phone');
							$n_item_id = $s_ins_id;
							$n_hash = md5(time());
							$n_item = 'personal';
							$n_title = 'New Personal Savings';
							$n_details = 'Congratulation! You have just created a new savings <b>('.ucwords($name).')</b> with target of '.$this->country_curr.number_format($target,2).' for the duration of '.$duration.' '.$type.'<br /><br />Please note that you will need to be contributing '.$this->country_curr.number_format((float)$contribute_amt,2).' '.$contribute.' to meet the target.';
							
							$this->Crud->notify($n_user_id, $n_othername, $n_email, $n_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
						} else {
							$data['err_msg'] = $this->Crud->msg('danger', 'Please try later');
						}
					}
					
				} if(isset($_POST['btnAccount'])){
					$p_save_id = $_POST['p_save_id'];
					$acc_id = $_POST['acc_id'];
					$bank = $_POST['bank'];
					$acc_no = $_POST['acc_no'];
					$acc_name = $_POST['acc_name'];
					$acc_desc = $_POST['acc_desc'];
					
					if($acc_id == ''){
						// register new account and link
						if($this->Crud->check3('user_id', $user_id, 'bank_id', $bank, 'acc_no', $acc_no, 'ka_account') <= 0){
							// register account
							$acc_data = array(
								'user_id' => $user_id,
								'bank_id' => $bank,
								'acc_no' => $acc_no,
								'acc_name' => $acc_name,
								'acc_desc' => $acc_desc,
								'reg_date' => $now
							);
							$acc_ins_id = $this->Crud->create('ka_account', $acc_data);
							if($acc_ins_id){
								$acc_id = $acc_ins_id;
							} else {
								$acc_id = 0;
							}
						}
					}
					
					// link account
					$link_data = array(
						'user_id' => $user_id,
						'acc_id' => $acc_id,
						'saving_id' => $p_save_id,
						'reg_date' => $now
					);
					$link_ins_id = $this->Crud->create('ka_account_link', $link_data);
					if($link_ins_id){
						if($acc_id == 0){
							$data['err_msg'] = $this->Crud->msg('success', 'Perfect! - Your savings is created, but you have to manually link account <a href="'.base_url('savings/personal/v'.$p_save_id).'">HERE</a>. You can also get upto 7% money back if you use this savings to buy on Jumia - <a href="'.base_url('offer/lists').'>Subscribe Now</a>');
						} else {
							$data['err_msg'] = $this->Crud->msg('success', 'Perfect! - Your savings is created and Account linked. <a href="'.base_url('savings/personal/v'.$p_save_id).'">Start Savings Now</a>. You can also get upto 7% money back if you use this savings to buy on Jumia - <a href="'.base_url('offer/lists').'>Subscribe Now</a>');
						}
					}
					
					// redirect to contribution page
					redirect(base_url('savings/personal/p'.$p_save_id), 'refresh');
				}
				
			}
			
			$data['allacc'] = $this->Crud->read_single_order('user_id', $user_id, 'ka_account', 'acc_name', 'ASC');
			$data['allbank'] = $this->Crud->read_order('ka_bank', 'name', 'ASC');
			$data['allcountry'] = $this->Crud->read_order('ka_country', 'name', 'ASC');
			
			$data['title'] = 'Create Personal Savings | '.app_name;
			$data['page_active'] = 'personal';
			
			$this->load->view('designs/header', $data);
			$this->load->view('savings/add_personal', $data);
			$this->load->view('designs/footer', $data);
		} else if($param1 == 'v'){
		//////////////// READ ////////////////////////////////////////////
			if($param2 == ''){
				redirect(base_url('savings/personal'), 'refresh');
			} else {
				if($this->Crud->check2('id', $param2, 'user_id', $user_id, 'ka_personal') <= 0){
					redirect(base_url('savings/personal'), 'refresh');
				} else {
					if($_POST){
						// change account post
						if(isset($_POST['btnChangeAccount'])){
							$p_save_id = $_POST['p_save_id'];
							$acc_id = $_POST['acc_id'];
							$bank = $_POST['bank'];
							$acc_no = $_POST['acc_no'];
							$acc_name = $_POST['acc_name'];
							$acc_desc = $_POST['acc_desc'];
							
							// first remove current link before linking another
							$this->Crud->delete('saving_id', $p_save_id, 'ka_account_link');
							
							if($acc_id == ''){
								// register new account and link
								if($this->Crud->check3('user_id', $user_id, 'bank_id', $bank, 'acc_no', $acc_no, 'ka_account') <= 0){
									// register account
									$acc_data = array(
										'user_id' => $user_id,
										'bank_id' => $bank,
										'acc_no' => $acc_no,
										'acc_name' => $acc_name,
										'acc_desc' => $acc_desc,
										'reg_date' => $now
									);
									$acc_ins_id = $this->Crud->create('ka_account', $acc_data);
									if($acc_ins_id){
										$acc_id = $acc_ins_id;
									} else {
										$acc_id = 0;
									}
								}
							}
							
							// link account
							$link_data = array(
								'user_id' => $user_id,
								'acc_id' => $acc_id,
								'saving_id' => $p_save_id,
								'reg_date' => $now
							);
							$link_ins_id = $this->Crud->create('ka_account_link', $link_data);
							if($link_ins_id){
								if($acc_id == 0){
									$data['err_msg'] = $this->Crud->msg('warning', 'Account not linked, please try again');
								} else {
									$data['err_msg'] = $this->Crud->msg('success', 'Perfect! - Account linked successfully');
								}
							}
						}
					}
					
					
					$data['allpersonal'] = $this->Crud->read2('id', $param2, 'user_id', $user_id, 'ka_personal');
					$data['allacc'] = $this->Crud->read_single_order('user_id', $user_id, 'ka_account', 'acc_name', 'ASC');
					$data['allbank'] = $this->Crud->read_order('ka_bank', 'name', 'ASC');
						
					$data['title'] = 'Personal Savings | '.app_name;
					$data['page_active'] = 'personal';
					
					$this->load->view('designs/header', $data);
					$this->load->view('savings/view_personal', $data);
					$this->load->view('designs/footer', $data);
				}
			}
		} else if($param1 == 'p'){
		//////////////// START SAVINGS //////////////////////////////////////////
			if($param2 == ''){
				redirect(base_url('savings/personal'), 'refresh');
			} else {
				if($this->Crud->check2('id', $param2, 'user_id', $user_id, 'ka_personal') <= 0){
					redirect(base_url('savings/personal'), 'refresh');
				} else {
					if($_POST){
						// get card details
						if(isset($_POST['btnChangeAccount'])){
							$pt_saving_id = $_POST['saving_id'];
							if(isset($_POST['togglevalue'])){$pt_togglevalue = $_POST['togglevalue'];} else {$pt_togglevalue = '';}
							if(isset($_POST['datepicker'])){$datepicker = $_POST['datepicker'];} else {$datepicker = '';}
							$pt_amount = $_POST['amount'];
							if(isset($_POST['card_name'])){$pt_card_name = $_POST['card_name'];} else {$pt_card_name = '';}
							if(isset($_POST['card_no'])){$pt_card_no = $_POST['card_no'];} else {$pt_card_no = '';}
							if(isset($_POST['exp_month'])){$pt_exp_month = $_POST['exp_month'];} else {$pt_exp_month = '';}
							if(isset($_POST['exp_year'])){$pt_exp_year = $_POST['exp_year'];} else {$pt_exp_year = '';}
							if(isset($_POST['cvv'])){$pt_cvv = $_POST['cvv'];} else {$pt_cvv = '';}
							if(isset($_POST['save_card'])){$save_card = $_POST['save_card'];} else {$save_card = '';}
							if(isset($_POST['card_id'])){$card_id = $_POST['card_id'];} else {$card_id = '';}
							$pt_user_phone = '+234'.substr($user_phone, -10);
							
							if($pt_togglevalue){$starting = $now;} else {$starting = date(fdate, strtotime($datepicker));}
							
							if(isset($_POST['datepicker'])){
								$datepicker = $_POST['datepicker'];
							} else {
								$getst = $this->Crud->read_single('id', $param2, 'ka_personal');
								if(!empty($getst)){
									foreach($getst as $st){
										$starting = $st->saving_start;	
									}
								}
							}
							
							// try and save card if checked
							if($save_card != ''){
								// check if not saved already
								if($pt_card_name!='' && $pt_card_no!='' && $pt_cvv!='' && $pt_exp_month!='' && $pt_exp_year!=''){
									$chksc = $this->Crud->check3('user_id', $user_id, 'no', $pt_card_no, 'cvv', $pt_cvv, 'ka_card');
									if($chksc <= 0){
										$card_ins_data = array(
											'user_id' => $user_id,
											'name' => $pt_card_name,
											'no' => $pt_card_no,
											'cvv' => $pt_cvv,
											'exp_month' => $pt_exp_month,
											'exp_year' => $pt_exp_year,
											'reg_date' => $now,
										);	
										$card_id = $this->Crud->create('ka_card', $card_ins_data);
									}
								}
							}
							
							// if saved card is selected, use it instead
							if($card_id != ''){
								// update card id in personal
								$ucp_data = array('card_id' => $card_id);
								$this->Crud->update('id', $pt_saving_id, 'ka_personal', $ucp_data);
								
								$getucard = $this->Crud->read_single('id', $card_id, 'ka_card');
								if(!empty($getucard)){
									foreach($getucard as $ucard){
										$pt_card_name = $ucard->name;
										$pt_card_no = $ucard->no;
										$pt_exp_month = $ucard->exp_month;
										$pt_exp_year = $ucard->exp_year;
										$pt_cvv = $ucard->cvv;
									}
								}
							}
							
							// get token and post request
							$gettoken = json_decode($this->Crud->pay_token());
							if($gettoken) {
								if($gettoken->status == 'success'){
									$pt_fee = 100; // default for Nigerians
									if($this->country_name == 'Nigeria') {
										$pt_fee = 100;
									} else if($this->country_name == 'Ghana') {
										$pt_fee = 40;
									} else if($this->country_name == 'Kenya') {
										$pt_fee = 500;
									}
									
									$redirecturl = base_url('savings/notify');
									
									$getpay = json_decode($this->Crud->pay_card_to_wallet($user_othername, $user_lastname, $pt_user_phone, $user_email, $pt_card_no, $pt_cvv, $pt_exp_year, $pt_exp_month, $pt_amount, $pt_fee, $this->country_code, $redirecturl, $gettoken->token), true);
									if($getpay){
										if($getpay['status'] == 'success'){
											// try and register transaction
											$ins_trans_data = array(
												'user_id' => $user_id,
												'item_id' => $pt_saving_id,
												'pay_code' => time().rand(),
												'type' => $getpay['data']['transfer']['type'],
												'commence' => $starting,
												'recipient' => 'wallet',
												'card_name' => $pt_card_name,
												'card_no' => $pt_card_no,
												'cvv' => $pt_cvv,
												'exp_month' => $pt_exp_month,
												'exp_year' => $pt_exp_year,
												'card_id' => $card_id,
												'api_key' => '',
												'amount' => $getpay['data']['transfer']['netDebitAmount'],
												'fee' => $getpay['data']['transfer']['merchantCommission'],
												'medium' => $getpay['data']['transfer']['medium'],
												'trnx_id' => $getpay['data']['transfer']['id'],
												'trnx_ref' => $getpay['data']['transfer']['flutterChargeReference'],
												'trnx_status' => 'pending',
												'reg_date' => $now,
											);
											$this->Crud->create('ka_transaction', $ins_trans_data);
											
											// create session token for genuine transaction
											$s_data = array('kas_new_trans' => true);
											$this->session->set_userdata($s_data);
											
											$data['pay_auth_url'] = $getpay['data']['authurl'];
											$data['err_msg'] = $this->Crud->msg('info', $getpay['data']['responsehtml']);
										} else {
											$data['err_msg'] = $this->Crud->msg('warning', $getpay['message']);
										}
									}
								}
							}
						}
					}
					
					$data['allpersonal'] = $this->Crud->read2('id', $param2, 'user_id', $user_id, 'ka_personal');
					$data['allcard'] = $this->Crud->read_single('user_id', $user_id, 'ka_card');
					$data['allacc'] = $this->Crud->read_single_order('user_id', $user_id, 'ka_account', 'acc_name', 'ASC');
					$data['allbank'] = $this->Crud->read_order('ka_bank', 'name', 'ASC');
						
					$data['title'] = 'Schedule Savings | '.app_name;
					$data['page_active'] = 'personal';
					
					$this->load->view('designs/header', $data);
					$this->load->view('savings/pay_personal', $data);
					$this->load->view('designs/footer', $data);
				}
			}
		} else if($param1 == 'w'){
		//////////////// WITHDRAW SAVINGS //////////////////////////////////////////
			if($param2 == ''){
				redirect(base_url('savings/personal'), 'refresh');
			} else {
				if($this->Crud->check2('id', $param2, 'user_id', $user_id, 'ka_personal') <= 0){
					redirect(base_url('savings/personal'), 'refresh');
				} else {
					if($_POST){
						$w_acc_id = $_POST['acc_id'];
						$w_amount = $_POST['amount'];
						$w_confirm_amt = $this->session->userdata('kas_disamt');
						$w_confirm_acc = $this->session->userdata('kas_disacc');
						$w_sender = strtoupper($user_othername).' '.strtoupper($user_lastname);
						
						if($w_acc_id == '' || $w_amount == '' || $w_confirm_amt == ''){
							$data['err_msg'] = $this->Crud->msg('warning', 'You are yet to link Disbursement Account');
						} else {
							if($w_confirm_amt != $w_amount || $w_confirm_acc != $w_acc_id) {
								$data['err_msg'] = $this->Crud->msg('danger', 'Invalid amount requested');
							} else {
								// get account number and bank code
								$w_acc_no = '';
								$w_bank_code = '';
								$w_acc_name = '';
								$w_acc_desc = '';
								$getacc = $this->Crud->read_single('id', $w_acc_id, 'ka_account');	
								if(!empty($getacc)){
									foreach($getacc as $acc){
										$w_acc_no = $acc->acc_no;
										$w_acc_name = $acc->acc_name;
										$w_acc_desc = $acc->acc_desc;
										
										// get bank code
										$getbnk = $this->Crud->read_single('id', $acc->bank_id, 'ka_bank');	
										if(!empty($getbnk)){
											foreach($getbnk as $bnk){
												$w_bank_code =  $bnk->code;
												$w_bank_name =  $bnk->name;
											}
										}
									}
								}
								
								// get savings withdraw amount
								$gp_save_amt = 0;
								$getpers = $this->Crud->read_single('id', $param2, 'ka_personal');
								if(!empty($getpers)){
									foreach($getpers as $pers){
										$gp_target = $pers->target;
										$gp_saving = $pers->saving;	
										$gp_cycle = $pers->cycle;	
										$gp_complete = $pers->complete;
										
										if($gp_complete == 1){
											$gp_save_amt = $gp_target;
										} else {
											$gp_save_amt = $gp_saving * $gp_cycle;
										}
									}
								}
								
								if($w_acc_no && $w_bank_code) {
									// get token and validate account
									$gettoken = json_decode($this->Crud->pay_token());
									if($gettoken) {
										if($gettoken->status == 'success'){
											$success = 0;
											$save_wt_id = 0;
											$getdisbur = json_decode($this->Crud->pay_wallet_to_account($w_amount, $w_bank_code, $w_acc_no, $this->country_code, $w_sender, $gettoken->token), true);
											if($getdisbur){
												if($getdisbur['status'] == 'success'){
													if($getdisbur['data']['data']['responsecode'] != 00){
														$data['err_msg'] = $this->Crud->msg('info', 'Can not process now, please try later');
														$resp_msg = $getdisbur['message'];
														$resp_ref = $getdisbur['code'];
														$data['err_msg'] = $this->Crud->msg('danger', 'Transaction Failed! - '.$resp_msg);
													} else {
														$resp_msg = $getdisbur['data']['data']['responsemessage'];
														$resp_ref = $getdisbur['data']['data']['uniquereference'];
														$success = 1;
														
														// save withdraw
														$ins_wt_data = array(
															'user_id' => $user_id,
															'item_id' => $param2,
															'purpose' => 'personal',
															'type' => 'withdraw',
															'amt' => $gp_save_amt,
															'send_to' => $w_acc_name.' ['.$w_acc_no.'] - '.ucwords($w_acc_desc),
															'bank' => $w_bank_name,
															'success' => $success,
															'status' => $resp_msg,
															'reg_date' => $now
														);
														$save_wt_id = $this->Crud->create('ka_vault', $ins_wt_data);
														if($save_wt_id != 0){
															$data['err_msg'] = $this->Crud->msg('success', 'Disbursement Processed! - '.$resp_msg);
															// update savings disbursed
															$p_disb_data = array('disbursed' => 1);
															$this->Crud->update('id', $param2, 'ka_personal', $p_disb_data);	
														}
													}
												} else {
													$resp_msg = $getdisbur['message'];
													$resp_ref = '';
													$data['err_msg'] = $this->Crud->msg('danger', 'Transaction Failed! - '.$resp_msg);
												}
											}
											
											// try and register transaction
											$wt_trans_data = array(
												'user_id' => $user_id,
												'item_id' => $param2,
												'pay_code' => time().rand(),
												'type' => 'debit-wallet',
												'recipient' => $w_acc_name.' ['.$w_acc_no.']',
												'amount' => $w_amount,
												'medium' => 'account',
												'fee' => 45,
												'trnx_ref' => $resp_ref,
												'trnx_status' => $resp_msg,
												'reg_date' => $now,
											);
											$wt_save_trans_id = $this->Crud->create('ka_transaction', $wt_trans_data);
											
											if($save_wt_id != 0){
												$upd_wt_data = array('trans_id'=>$wt_save_trans_id);
												$this->Crud->update('id', $save_wt_id, 'ka_vault', $upd_wt_data);
											}
										}
									}
								}
							}
						}
					}
					
					$data['allpersonal'] = $this->Crud->read2('id', $param2, 'user_id', $user_id, 'ka_personal');
						
					$data['title'] = 'Withdraw Savings | '.app_name;
					$data['page_active'] = 'personal';
					
					$this->load->view('designs/header', $data);
					$this->load->view('savings/withdraw_personal', $data);
					$this->load->view('designs/footer', $data);
				}
			}
		} else if($param1 == 'edit'){
		//////////////// UPDATE //////////////////////////////////////////
		
		} else if($param1 == 'delete'){
		///////////////// DELETE ////////////////////////////////////////
			if($param2 != ''){
				if($_POST){
					$del_id = $_POST['del_id'];
					$del_name = $_POST['del_name'];
					
					echo $del_name.' Deleted';	
				} exit;
			}
		} else {
			$data['allpersonal'] = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
				
			$data['title'] = 'Personal Savings | '.app_name;
			$data['page_active'] = 'personal';
			
			$this->load->view('designs/header', $data);
			$this->load->view('savings/personal', $data);
			$this->load->view('designs/footer', $data);
		}
	}
	
	///////////// ************ END PERSONAL SAVINGS ************* /////////////////////
	
	///////////// ************ CONTIBUTIONS ************* /////////////////////
	public function contribution() {
		$data['my_curr'] = $this->country_curr; // pass to views
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$data['allpersonal'] = $this->Crud->read_single('user_id', $user_id, 'ka_personal');
		
		$data['title'] = 'Contributions | '.app_name;
		$data['page_active'] = 'contribution';
		
		$this->load->view('designs/header', $data);
		$this->load->view('savings/contribution', $data);
		$this->load->view('designs/footer', $data);
	}
	///////////// ************ END CONTIBUTIONS ************* /////////////////////
	
	///////////// ************ PAYMENT NOTIFICATION ************* /////////////////////
	public function notify() {
		$data['my_curr'] = $this->country_curr; // pass to views
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$now = date(fdate);
		$rc = $this->input->get('rc');
		$status = $this->input->get('transactionStatus');
		$msg = $this->input->get('responseMessage');
		$id = $this->input->get('id');
		$ref = $this->input->get('ref');
		$re_query = 'savings/notify?rc='.$rc.'&transactionStatus='.$status.'&responseMessage='.$msg.'&id='.$id.'&ref='.$ref;
		
		$p_err_msg = '';
		$p_status_ico = '';
		$p_msg = '';
		
		// get notification response
		if($rc=='' || $status=='' || $msg=='' || $id=='' || $ref==''){
			$p_err_msg = 'Inavlid Transaction Record';
			$p_status_icon = 'warming.svg';
			$p_msg = 'No Record';
		} else {
			$getnotify = $this->Crud->read3('user_id', $user_id, 'trnx_id', $id, 'trnx_ref', $ref, 'ka_transaction');
			if(!empty($getnotify)){
				if($rc!=00){
					$p_err_msg = $msg;
					$p_status_icon = 'cancel.svg';
					$p_msg = 'Transaction Failed';
				} else {
					$p_err_msg = $msg;
					$p_status_icon = 'success.svg';
					$p_msg = 'Transaction Successful';
					
					// verify transaction
					$gettoken = json_decode($this->Crud->pay_token());
					if($gettoken) {
						if($gettoken->status == 'success'){
							$getver = json_decode($this->Crud->pay_verify($id, $gettoken->token), true);
							if($getver){
								if($getver['status'] == 'success'){
									// now update transaction
									$trans_upd_data = array('trnx_status' => $getver['data']['status'], 're_query'=>$re_query);
									$this->Crud->update('trnx_id', $id, 'ka_transaction', $trans_upd_data);
									
									// now perform savings logic
									if($getver['data']['status'] == 'completed'){
										foreach($getnotify as $getn){
											if($getn->type == 'fund-wallet'){
												$getsavings = $this->Crud->read_single('id', $getn->item_id, 'ka_personal');
											}
											
											if(!empty($getsavings)){
												foreach($getsavings as $gsave){
													$pid = $gsave->id;
													$pname = $gsave->name;
													$cycle = $gsave->cycle;	
													$duration = $gsave->duration;
													
													if($this->session->userdata('kas_new_trans') == false) {
														$p_err_msg = 'Contribution already made for Recent Cycle. Next Contribution Cycle is '.date('d M, Y', strtotime($gsave->saving_next)).'. Thank you';	
													} else {
														// check first time contibution
														if($gsave->cycle == 0){
															$saving_start = $getn->commence;
															if($gsave->type == 'Monthly') {
																$saving_end = date(fdate, strtotime('+'.$gsave->duration.' month', strtotime($saving_start)));
																$saving_next = date(fdate, strtotime('+1 month', strtotime($saving_start)));	
															} else if($gsave->type == 'Weekly') {
																$saving_end = date(fdate, strtotime('+'.$gsave->duration.' week', strtotime($saving_start)));
																$saving_next = date(fdate, strtotime('+1 week', strtotime($saving_start)));		
															} else if($gsave->type == 'Daily') {
																$saving_end = date(fdate, strtotime('+'.$gsave->duration.' day', strtotime($saving_start)));	
																$saving_next = date(fdate, strtotime('+1 day', strtotime($saving_start)));	
															}
															$saving_current = $saving_start;
														} else {
															$saving_start = $gsave->saving_start;
															$saving_end = $gsave->saving_end;
															$saving_current = $now;
															
															// recalculate end date to care so contribution day ommition
															$day_left = $duration - $cycle;
															
															if($gsave->type == 'Monthly') {
																$saving_next = date(fdate, strtotime('+1 month', strtotime($now)));	
																$saving_end = date(fdate, strtotime('+'.$day_left.' month', strtotime($now)));	
															} else if($gsave->type == 'Weekly') {
																$saving_next = date(fdate, strtotime('+1 week', strtotime($now)));		
																$saving_end = date(fdate, strtotime('+'.$day_left.' week', strtotime($now)));	
															} else if($gsave->type == 'Daily') {
																$saving_next = date(fdate, strtotime('+1 day', strtotime($now)));	
																$saving_end = date(fdate, strtotime('+'.$day_left.' day', strtotime($now)));	
															}
														}
														
														// check if circle not completed
														if($gsave->complete == 0){
															$con_data = array(
																'saving_id' => $gsave->id,
																'amt' => $gsave->saving,
																'type' => 'Debit Card',
																'trans_id' => $getn->id,
																'reg_date' => $now
															);
															$con_ins = $this->Crud->create('ka_contribute'	, $con_data);
															if($con_ins) {
																$p_err_msg = 'Contribution done, your next cycle is '.date('d M, Y', strtotime($saving_next));
																// now update savings table
																$cycle = $gsave->cycle+1;
																if($cycle >= $gsave->duration){
																	$complete = 1; $expired = 1; $active = 0;
																	$p_err_msg = 'Contribution completed, Please visit this savings and instruct us to move your fund to specified Account. Thank you';
																} else {
																	$complete = 0; $expired = 0; $active = 1;
																}
																$sav_data = array(
																	'saving_start' => $saving_start,
																	'saving_end' => $saving_end,
																	'saving_next' => $saving_next,
																	'saving_current' => $saving_current,
																	'cycle' => $cycle,
																	'complete' => $complete,
																	'active' => $active,
																	'expired' => $expired,
																);
																$this->Crud->update('id', $gsave->id, 'ka_personal', $sav_data);
																
																// now save to wallet
																$wal_data = array(
																	'user_id' => $user_id,
																	'item_id' => $gsave->id,
																	'purpose' => 'personal',
																	'type' => 'save',
																	'amt' => $gsave->saving,
																	'reg_date' => $now
																);
																$this->Crud->create('ka_vault'	, $wal_data);
																
																// save and send notification
																$n_user_id = ''; $n_othername = ''; $n_email = ''; $n_phone = '';
																$getnuser = $this->Crud->read_single('id', $user_id, 'ka_user');
																if(!empty($getnuser)){
																	foreach($getnuser as $nuser){
																		$n_user_id = $nuser->id;
																		$n_othername = ucwords($nuser->othername);
																		$n_lastname = ucwords($nuser->lastname);
																		$n_email = $nuser->email;
																		$n_phone = $nuser->phone;	
																	}
																}
																$n_item_id = $gsave->id;
																$n_hash = md5(time());
																$n_item = 'personal';
																$n_title = 'Personal Savings';
																$n_details = 'Yo\'ve just completed '.$cycle.' of '.$gsave->duration.' cycle of your '.ucwords($pname).' savings';
																
																$this->Crud->notify($n_user_id, $n_othername, $n_email, $n_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
																
																// notify admins
																$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
																$abody = '
																	<div class="mname">Dear '.app_name.',</div><br />
																	<b>'.$n_othername.' '.$n_lastname.'</b> just have completed is '.$cycle.' of '.$gsave->duration.' cycle of your '.ucwords($pname).' savings.<br /><br />
																	Warm Regards
																';
																$this->Crud->send_email($admin_list, app_email, 'Contribution Notification', $abody, app_name, 'Payment Notification');
															} else {
																$data['err_msg'] = $this->Crud->msg('warning', 'There is problem! Try later');
															}
														}
														
														// clean session token to avoid redundancy
														$s_data = array('kas_new_trans' => false);
														$this->session->set_userdata($s_data);
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
			} else {
				$p_err_msg = 'Wrong Transaction Record';
				$p_status_icon = 'warming.svg';
				$p_msg = 'No Record Found';
			}
		}
		
		$data['err_msg'] = $p_err_msg;
		$data['status_icon'] = $p_status_icon;
		$data['msg'] = $p_msg;
		
		$data['title'] = 'Payment Notification | '.app_name;
		$data['page_active'] = 'contribution';
		
		$this->load->view('designs/frame_header', $data);
		$this->load->view('savings/notify', $data);
		$this->load->view('designs/frame_footer', $data);
	}
	///////////// ************ END PAYMENT NOTIFICATION ************* /////////////////////
	
	///////////// ************ VALIDATE ACCOUNT ************* /////////////////////
	public function validate_account() {
		if($_POST){
			$bank = $_POST['bank'];	
			$acc_no = $_POST['acc_no'];	
			
			// get bank code
			$acc_name = '';
			$getbank = $this->Crud->read_single('id', $bank, 'ka_bank');
			if(!empty($getbank)){
				foreach($getbank as $gbank){
					$bank_code = $gbank->code;
					
					// get token and validate account
					$gettoken = json_decode($this->Crud->pay_token());
					if($gettoken) {
						if($gettoken->status == 'success'){
							$getacc = json_decode($this->Crud->pay_validate($acc_no, $bank_code, $gettoken->token));
							if($getacc){
								if($getacc->status == 'success'){
									$acc_data = $getacc->data;
									foreach($acc_data as $key => $value){
										$acc_name = $value;
									}
								}
							}
						}
					}
				}
			}
			
			if($acc_name == ''){
				echo 'Please Try Later!';
			} else {
				echo $acc_name;
			}
		} exit;
	}
	///////////// ************ END VALIDATE ACCOUNT ************* /////////////////////
}
