<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vaults extends CI_Controller {

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
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
		}
		
		$data['allvault'] = $this->Crud->read_single('user_id', $user_id, 'ka_vault');
		$data['alloffer'] = $this->Crud->read_single('user_id', $user_id, 'ka_offer');
		$data['allvoluntary'] = $this->Crud->read_single('user_id', $user_id, 'ka_voluntary');
		
		$data['title'] = 'Vaults | '.app_name;
		$data['page_active'] = 'vault';
		
		$this->load->view('designs/header', $data);
		$this->load->view('vaults/vault', $data);
		$this->load->view('designs/footer', $data);
	}
	
	///////////// ************ FUND VOLUTARY VAULT ************* /////////////////////
	public function add_fund() {
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
		
		if($_POST){
			$pt_amount = $_POST['amount'];
			if(isset($_POST['card_name'])){$pt_card_name = $_POST['card_name'];} else {$pt_card_name = '';}
			if(isset($_POST['card_no'])){$pt_card_no = $_POST['card_no'];} else {$pt_card_no = '';}
			if(isset($_POST['exp_month'])){$pt_exp_month = $_POST['exp_month'];} else {$pt_exp_month = '';}
			if(isset($_POST['exp_year'])){$pt_exp_year = $_POST['exp_year'];} else {$pt_exp_year = '';}
			if(isset($_POST['cvv'])){$pt_cvv = $_POST['cvv'];} else {$pt_cvv = '';}
			if(isset($_POST['save_card'])){$save_card = $_POST['save_card'];} else {$save_card = '';}
			if(isset($_POST['card_id'])){$card_id = $_POST['card_id'];} else {$card_id = '';}
			$pt_user_phone = '+234'.substr($user_phone, -10);
			
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
			
			if($card_id != '') {
				// register data in vountary vault
				$vv_data = array (
					'user_id' => $user_id,
					'amount' => $pt_amount,
					'type' => 'Fund',
					'purpose' => 'Self Funding',
					'action' => 'Save',
					'trans_status' => 'Pending',
					'trans_msg' => 'Payment Process Not Completed',
					'reg_date' => $now
				);
				$pt_item_id = $this->Crud->create('ka_voluntary', $vv_data);
				
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
						
						$redirecturl = base_url('vaults/notify');
						
						$getpay = json_decode($this->Crud->pay_card_to_wallet($user_othername, $user_lastname, $pt_user_phone, $user_email, $pt_card_no, $pt_cvv, $pt_exp_year, $pt_exp_month, $pt_amount, $pt_fee, $this->country_code, $redirecturl, $gettoken->token), true);
						if($getpay){
							if($getpay['status'] == 'success'){
								// try and register transaction
								$ins_trans_data = array(
									'user_id' => $user_id,
									'item_id' => $pt_item_id,
									'item_type' => 'Voluntary',
									'pay_code' => time().rand(),
									'type' => $getpay['data']['transfer']['type'],
									'commence' => '',
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
		
		$data['allcard'] = $this->Crud->read_single('user_id', $user_id, 'ka_card');
		
		$data['title'] = 'Add Fund | '.app_name;
		$data['page_active'] = 'vault';
		
		$this->load->view('designs/header', $data);
		$this->load->view('vaults/add_fund', $data);
		$this->load->view('designs/footer', $data);
	}
	///////////// ************ END FUND VOLUTARY VAULT ************* /////////////////////
	
	///////////// ************ WITHDRAW FUND ************* /////////////////////
	public function withdraw_fund() {
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
		
		$data['confirm_fund'] = false;
		$now = date(fdate);
		
		// get user voluntary vault balance
		$vv_balance = 0;
		$vv_withdrawn = 0;
		$getvv = $this->Crud->read_single('user_id', $user_id, 'ka_voluntary');
		if(!empty($getvv)) {
			foreach($getvv as $vol) {
				$vv_action = $vol->action;
				$vv_trans_status = $vol->trans_status;
				$vv_amt = $vol->amount;
				
				if(strtolower($vv_action) == 'save' && strtolower($vv_trans_status) == 'success'){
					$vv_balance += (float)$vv_amt;
				} else if(strtolower($vv_action) == 'withdrawn') {
					$vv_withdrawn += (float)$vv_amt;
				}
			}
			$vv_balance = $vv_balance - $vv_withdrawn;
		}
		$data['vv_balance'] = $vv_balance;
		
		if($_POST){
			// check if confirm button clicked
			if(isset($_POST['btnConfirm'])){
				$w_bank = $_POST['c_bank'];
				$w_bank_name = $_POST['c_bank_name'];
				$w_bank_code = $_POST['c_bank_code'];
				$w_acc_no = $_POST['c_acc_no'];
				$w_acc_name = $_POST['c_acc_name'];
				$w_amount = $_POST['c_amount'];
				$w_sender = strtoupper($user_othername).' '.strtoupper($user_lastname);
				
				// now check if voluntary vault balance is sufficient
				if($w_amount > $vv_balance) {
					$data['err_msg'] = $this->Crud->msg('danger', 'Insufficient Fund. <a href="'.base_url('vaults/add_fund').'" class="btn btn-success btn-xs">Add Fund</a>');	
				} else {
					// now fund transfer logic
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
												'amount' => $w_amount,
												'type' => 'Fund',
												'purpose' => 'Fund sent to '.$w_acc_name.' ['.$w_acc_no.' - '.$w_bank_name.']',
												'action' => 'Withdrawn',
												'trans_status' => 'Completed',
												'trans_msg' => $resp_msg,
												'reg_date' => $now
											);
											$save_wt_id = $this->Crud->create('ka_voluntary', $ins_wt_data);
											if($save_wt_id != 0){
												$data['err_msg'] = $this->Crud->msg('success', 'Fund Transferred! - '.$resp_msg);
												
												// try and register transaction
												$wt_trans_data = array(
													'user_id' => $user_id,
													'item_id' => $save_wt_id,
													'item_type' => 'Voluntary',
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
													$this->Crud->update('id', $save_wt_id, 'ka_voluntary', $upd_wt_data);
												}	
												
												// prepare push notification
												$n_user_id = $user_id;
												$n_othername = $user_othername;
												$n_email = $user_email;
												$n_phone = $user_phone;
												$n_item_id = $save_wt_id;
												$n_hash = md5(time());
												$n_item = 'voluntary';
												$n_title = '[Fund Transfer] Voluntary Vault';
												$n_details = 'You have successfully withdrawn '.$this->country_curr.number_format($w_amount,2).' from your Voluntary Vault and transferred to '.$w_acc_name.' ['.$w_acc_no.' - '.$w_bank_name.']';
												
												$this->Crud->notify($n_user_id, $n_othername, $n_email, $n_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
												
												// notify admins
												$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
												$abody = '
													<div class="mname">Dear '.app_name.',</div><br />
													<b>'.$n_othername.' '.$n_lastname.'</b> just withdrawn '.$this->country_curr.number_format($w_amount,2).' from Voluntary Vault and transferred to '.$w_acc_name.' ['.$w_acc_no.' - '.$w_bank_name.'] .<br /><br />
													Warm Regards
												';
												$this->Crud->send_email($admin_list, app_email, 'Voluntary Vault Withdraw', $abody, app_name, 'Payment Notification');
											}
										}
									} else {
										$resp_msg = $getdisbur['message'];
										$resp_ref = '';
										$data['err_msg'] = $this->Crud->msg('danger', 'Transaction Failed! - '.$resp_msg);
									}
								}
							}
						}
					}	
				}
			} else {
				$acc_id = $_POST['acc_id'];
				$bank = $_POST['bank'];
				$acc_no = $_POST['acc_no'];
				$acc_name = $_POST['acc_name'];
				$acc_desc = $_POST['acc_desc'];
				$amount = $_POST['amount'];
				
				if($amount == '') {
					$data['err_msg'] = $this->Crud->msg('warning', 'Please Specify Account');
				} else {
					// check if new account details supplied
					if($bank != '' && $acc_no != '' && $acc_name != '') {
						$acc_id = 0; // important - deactive if account already selected to avoid confusion
						if($acc_desc == '') {
							$data['err_msg'] = $this->Crud->msg('warning', 'Please Describe Account');
						} else {
							$getacc = $this->Crud->read3('user_id', $user_id, 'bank_id', $bank, 'acc_no', $acc_no, 'ka_account');
							if(empty($getacc)){
								// register account
								$acc_data = array(
									'user_id' => $user_id,
									'bank_id' => $bank,
									'acc_no' => $acc_no,
									'acc_name' => $acc_name,
									'acc_desc' => $acc_desc,
									'reg_date' => $now
								);
								$acc_id = $this->Crud->create('ka_account', $acc_data);
							} else {
								foreach($getacc as $acc) {
									$acc_id = $acc->id;
								}
							}
						}
					} 
					
					if($acc_id) {
						$getacc = $this->Crud->read_single('id', $acc_id, 'ka_account');
						if(!empty($getacc)){
							foreach($getacc as $acc) {
								$data['c_bank'] = $acc->bank_id;
								$data['c_acc_no'] = $acc->acc_no;
								$data['c_acc_name'] = $acc->acc_name;
								$data['c_acc_desc'] = $acc->acc_desc;
								
								// get bank details
								$getbank = $this->Crud->read_single('id', $acc->bank_id, 'ka_bank');
								if(!empty($getbank)){
									foreach($getbank as $bk){
										$data['c_bank_name'] = $bk->name;
										$data['c_bank_code'] = $bk->code;	
									}
								}
								
								$data['c_amount'] = $amount;
								
								// activate confirmation
								$data['confirm_fund'] = true;
							}
						}
					}
				}
			}
		}
		
		$data['allacc'] = $this->Crud->read_single('user_id', $user_id, 'ka_account');
		$data['allbank'] = $this->Crud->read_order('ka_bank', 'name', 'ASC');
		
		$data['title'] = 'Withdraw Fund | '.app_name;
		$data['page_active'] = 'vault';
		
		$this->load->view('designs/header', $data);
		$this->load->view('vaults/withdraw_fund', $data);
		$this->load->view('designs/footer', $data);
	}
	///////////// ************ END WITHDRAW FUND ************* /////////////////////
	
	///////////// ************ FUND NOTIFICATION ************* /////////////////////
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
			$user_lastname = $this->session->userdata('kas_user_lastname');
			$user_othername = $this->session->userdata('kas_user_othername');
			$user_email = $this->session->userdata('kas_user_email');
			$user_phone = $this->session->userdata('kas_user_phone');
		}
		
		$now = date(fdate);
		$rc = $this->input->get('rc');
		$status = $this->input->get('transactionStatus');
		$msg = $this->input->get('responseMessage');
		$id = $this->input->get('id');
		$ref = $this->input->get('ref');
		$re_query = 'vaults/notify?rc='.$rc.'&transactionStatus='.$status.'&responseMessage='.$msg.'&id='.$id.'&ref='.$ref;
		
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
									
									// now update funding logic
									if($getver['data']['status'] == 'completed'){
										foreach($getnotify as $getn){
											if($getn->type == 'fund-wallet'){
												$vv_upd = array('trans_id' => $id, 'trans_status' => $status, 'trans_msg' => $msg,);
												if($this->Crud->update('id', $getn->item_id, 'ka_voluntary', $vv_upd) > 0){
													$p_err_msg = 'Your Voluntary Vault is now funded';
													
													// prepare push notification
													$n_user_id = $user_id;
													$n_othername = $user_othername;
													$n_email = $user_email;
													$n_phone = $user_phone;
													$n_item_id = $getn->item_id;
													$n_hash = md5(time());
													$n_item = 'voluntary';
													$n_title = 'Voluntary Vault';
													$n_details = 'You have successfully funded your Voluntary Vault, you can now use it for anything you wanted from your SusuAI Account';
													
													$this->Crud->notify($n_user_id, $n_othername, $n_email, $n_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
													
													// notify admins
													$admin_list = 'iyinusa@yahoo.co.uk, rlawal27@gmail.com';
													$abody = '
														<div class="mname">Dear '.app_name.',</div><br />
														<b>'.$n_othername.' '.$n_lastname.'</b> just funded Voluntary Vault.<br /><br />
														Warm Regards
													';
													$this->Crud->send_email($admin_list, app_email, 'Voluntary Vault Funding', $abody, app_name, 'Payment Notification');
												} else {
													$p_err_msg = 'There is problem funding your Voluntary Vault, we will look into it';	
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
		$data['page_active'] = 'vault';
		
		$this->load->view('designs/frame_header', $data);
		$this->load->view('vaults/notify', $data);
		$this->load->view('designs/frame_footer', $data);
	}
	///////////// ************ END PAYMENT NOTIFICATION ************* /////////////////////
}
