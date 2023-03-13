<?php
	$list = '';
	$disburse_list = '';
	$income_cost = 0;
	$disburse_cost = 0;
	$profit_margin = 0;
	$platform_cost = 0;
	if(!empty($alltrans)){
		foreach($alltrans as $trans){
			$id = $trans->id;
			$user_id = $trans->user_id;
			$item_id = $trans->item_id;
			$item_type = $trans->item_type;
			$pay_code = $trans->pay_code;
			$type = $trans->type;
			$amt = $trans->amount;
			$medium = $trans->medium;
			$recipient = $trans->recipient;
			$fees = $trans->fee;
			$status = $trans->trnx_status;
			$ref = $trans->trnx_ref;
			$reg_date = $trans->reg_date;
			
			// get transaction details
			$p_saving_by = '';
			$save_id = '';
			$save_name = '';
			$user_id = '';
			$trans_alert = '';
			$logo_path = 'assets/images/users/avatar300.png';
			if($type == 'fund-wallet' || $type == 'debit-wallet'){
				if($item_type == 'Voluntary') {
					$getsave = $this->Crud->read_single('id', $item_id, 'ka_voluntary');
				} else {
					$getsave = $this->Crud->read_single('id', $item_id, 'ka_personal');
				}
				
				if($type == 'fund-wallet') {
					$trans_alert = 'class="text-success"';	
				} else {
					$trans_alert = 'class="text-danger"';	
				}
			}
			
			// get savings details
			if(!empty($getsave)){
				foreach($getsave as $save){
					$save_id = $save->id;
					
					if($item_type == '') {
						$save_name = ucwords($save->name);
					} if($item_type == 'Voluntary') {
						$save_name = $save->purpose;
					}
					
					// get user detail
					$getuser = $this->Crud->read_single('id', $save->user_id, 'ka_user');
					if(!empty($getuser)){
						foreach($getuser as $user){
							$user_id = $user->id;
							$p_saving_by = ucwords($user->othername).' '.ucwords($user->lastname);	
							
							//get logo
							$getimg = $this->Crud->read_single('id', $user->pics, 'ka_img');
							if(!empty($getimg)){
								foreach($getimg as $img){
									$logo_path = $img->pics_square;	
								}
							}
						}
					}	
				}
			}
			
			$list .= '
				<tr '.$trans_alert.'>
					<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
					<td>'.$pay_code.'</td>
					<td><img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30"></td>
					<td><a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a></td>
					<td>'.ucwords($save_name).'</td>
					<td>'.ucwords($type).' ('.ucwords($medium).')</td>
					<td>'.number_format((float)$amt,2).'</td>
					<td>'.ucwords($status).'</td>
				</tr>
			';
			
			// get moneywave wallet transaction
			if($ref != '' && $type == 'debit-wallet') {
				$wtrans_amt = 0;
				$wtrans_msg = '';
				$wtrans_ref = '';
				$gettoken = json_decode($this->Crud->pay_token());
				if($gettoken) {
					if($gettoken->status == 'success'){
						$wallet_trans = json_decode($this->Crud->pay_wallet_transaction($ref, $gettoken->token));
						if($wallet_trans){
							if($wallet_trans->status == 'success'){
								$wallet_trans_data = $wallet_trans->data;
								foreach($wallet_trans_data as $tdata){
									//$wtrans_amt = $tdata->amount;
									//$wtrans_msg = $tdata->flutterResponseMessage;
									//$wtrans_ref = $tdata->flutterReference;
									$wtrans_amt = $amt;
									$wtrans_msg = $status;
									$wtrans_ref = $ref;
								}
							}
						}
					}
				}
				
				$disburse_list .= '
					<tr>
						<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
						<td>'.$wtrans_ref.'</td>
						<td><img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30"></td>
						<td><a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a></td>
						<td>'.$recipient.'</td>
						<td>'.number_format((float)$wtrans_amt,2).'</td>
						<td>'.$wtrans_msg.'</td>
					</tr>
				';
			}
			
			// sum up funds via flutterwave
			if($status == 'completed' && $type == 'fund-wallet'){
				$income_cost += $fees;
			}
			
			// sum up disbused cost via flutterwave
			if($status == 'Completed Successfully' && $type == 'debit-wallet'){
				$disburse_cost += $fees;
			}
		}
	}
	
	$profit_margin = $income_cost - $disburse_cost;
	$platform_cost = $income_cost;
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Transactions</h4>
            <p class="text-muted m-b-30">
                All transaction activities by users.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="widget-inline">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-primary mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($wallet_balance,2); ?></b></h4>
                            <p class="text-muted">MW Wallet Balance</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-info mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($platform_cost,2); ?></b></h4>
                            <p class="text-muted">Gross Margin</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-danger mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($disburse_cost,2); ?></b></h4>
                            <p class="text-muted">Disbursed Cost</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-success mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($profit_margin,2); ?></b></h4>
                            <p class="text-muted">Profit Margin</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <hr />
    
    <div class="m-t-30">
        <ul class="nav nav-tabs tabs-bordered">
            <li class="active"> <a href="#trans-rpt" data-toggle="tab" aria-expanded="true"> Transactions </a> </li>
            <li class=""> <a href="#disbursed-rpt" data-toggle="tab" aria-expanded="false"> Disbursed </a> </li>
        </ul>
        <div class="tab-content">
        	<div class="tab-pane active" id="trans-rpt">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="m-b-20 table-responsive">
                            <table id="datatable-buttons" class="table table-hover table-striped small">
                                <thead>
                                    <tr>
                                        <th><b>DATE</b></th>
                                        <th><b>OrderID</b></th>
                                        <th><b></b></th>
                                        <th><b>BY</b></th>
                                        <th><b>FOR</th>
                                        <th><b>PURPOSE</th>
                                        <th><b>AMT (<?php echo my_curr; ?>)</b></th>
                                        <th><b>STATUS</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $list; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="disbursed-rpt">
                <div class="row">
                    <div class="col-xs-12">
                        <?php if(!empty($err_msg)){echo $err_msg;} ?>
                        <div class="m-b-20 table-responsive">
                            <table id="datatable-buttons2" class="table table-hover table-striped small">
                                <thead>
                                    <tr>
                                        <th><b>DATE</b></th>
                                        <th><b>FW-REF</b></th>
                                        <th><b></b></th>
                                        <th><b>BY</b></th>
                                        <th><b>TO</th>
                                        <th><b>AMT (<?php echo my_curr; ?>)</b></th>
                                        <th><b>STATUS</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $disburse_list; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  	</div>
    <!-- end row -->
</div>
