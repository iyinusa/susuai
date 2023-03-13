<?php
	// main vault
	$lifetime = 0;
	$current = 0;
	$withdraw = 0;
	$refund = 0;
	$list = '';
	if(!empty($allvault)){
		foreach($allvault as $vault){
			$id = $vault->id;
			$user_id = $vault->user_id;
			$item_id = $vault->item_id;
			$purpose = $vault->purpose;
			$type = $vault->type;
			$amt = $vault->amt;
			$send_to = $vault->send_to;
			$bank = $vault->bank;
			$status = $vault->status;
			$reg_date = $vault->reg_date;
			
			if($type == 'save') {
				$lifetime = $lifetime + (float)$amt;
				$alert = 'success';
			} else if($type == 'withdraw') {
				$withdraw = $withdraw + (float)$amt;
				$alert = 'danger';
			} else if($type == 'refund') {
				$refund = $refund + (float)$amt;
				$alert = 'warning';
			}
			
			// get purpose details
			$purpose_name = '';
			if($purpose == 'personal'){
				$getpur = $this->Crud->read_single('id', $item_id, 'ka_personal');
				if(!empty($getpur)){
					foreach($getpur as $pur){
						$purpose_name = $pur->name.' Contribution';	
					}
				}
			}
			
			if($purpose_name == ''){$purpose_name = $purpose;}
			
			$disburse = '';
			if($send_to != ''){
				$disburse = $send_to.'<br/><span class="text-muted">'.$bank.'</span><br /><span class="small">'.$status.'</span>';	
			}
			
			// get listing
			$list .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
					<td><b>'.ucwords($purpose_name).'</b></td>
					<td>'.ucwords($type).'</td>
					<td>'.$disburse.'</td>
					<td class="text-right">'.number_format($amt,2).'</td>
				</tr>
			';
		}
		
		$current = $lifetime - $withdraw;
	}
	
	// offer stat
	$offer_pending = 0;
	$offer_approve = 0;
	$offer_withdrawn = 0;
	$offer_declined = 0;
	if(!empty($alloffer)){
		foreach($alloffer as $of){
			$interest = $of->interest;
			$status = $of->status;
			
			if($status == 'Pending') {
				$offer_pending += (float)$interest;
			} else if($status == 'Approved') {
				$offer_approve += (float)$interest;
			} else if($status == 'Withdrawn') {
				$offer_withdrawn += (float)$interest;
			} else if($status == 'Declined') {
				$offer_declined += (float)$interest;
			}
		}
	}
	
	// voluntary
	$voluntary_list = '';
	$alert = '';
	$v_total = 0;
	$v_current = 0;
	$v_withdrawn = 0;
	if(!empty($allvoluntary)) {
		foreach($allvoluntary as $vol) {
			$amt = $vol->amount;
			$type = $vol->type;
			$purpose = $vol->purpose;
			$action = $vol->action;
			$trans_msg = $vol->trans_msg;
			$trans_status = $vol->trans_status;
			$reg_date = $vol->reg_date;
			
			if(strtolower($action) == 'save' && strtolower($trans_status) == 'success'){
				$alert = 'success';
				$v_current += (float)$amt;
			} else if(strtolower($action) == 'withdrawn') {
				$alert = 'warning';	
				$v_withdrawn += (float)$amt;
			}
			
			$v_total += (float)$amt;
			
			// get listing
			$voluntary_list .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
					<td><b>'.$type.'</b></td>
					<td>'.$purpose.'</td>
					<td>'.$action.'</td>
					<td class="text-right">'.number_format((float)$amt,2).'</td>
					<td><b>'.ucwords($trans_status).'</b><br /><small class="text-muted">'.$trans_msg.'</small></td>
				</tr>
			';
		}
		$v_current = $v_current - $v_withdrawn;
		$v_total = $v_total - $v_withdrawn;
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Vault/Wallet</h4>
            <p class="text-muted m-b-30">
                Keep track of your account here.
            </p>
        </div>
    </div>
    
    <ul class="nav nav-tabs tabs-bordered">
        <li class="active"> <a href="#s-tab" data-toggle="tab" aria-expanded="true"> Savings </a> </li>
        <li class=""> <a href="#o-tab" data-toggle="tab" aria-expanded="false"> Offers </a> </li>
        <li class=""> <a href="#v-tab" data-toggle="tab" aria-expanded="false"> Voluntary </a> </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="s-tab">
            <div class="row">
                <div class="col-sm-12">
                	<b>Savings Vault</b> is the contribution transactions for the purpose of all your planned bills.
                </div>
                <div class="col-sm-12">
                    <div class="widget-inline">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-info mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($lifetime,2); ?></b></h4>
                                    <p class="text-muted">Lifetime</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-success mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($current,2); ?></b></h4>
                                    <p class="text-muted">Current</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-danger mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($withdraw,2); ?></b></h4>
                                    <p class="text-muted">Withdrawn</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center b-0">
                                    <h4><i class="text-warning mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($refund,2); ?></b></h4>
                                    <p class="text-muted">Refund</p>
                                </div>
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
            <!--end row -->
            <hr />
            <div class="row">
                <div class="col-xs-12">
                    <div class="m-b-20 table-responsive">
                        <table id="datatable-buttons" class="table table-hover table-striped small">
                            <thead>
                                <tr>
                                    <th><b>DATE</b></th>
                                    <th><b>PURPOSE</b></th>
                                    <th><b>TYPE</b></th>
                                    <th><b>DISBURSE</b></th>
                                    <th class="text-right"><b>AMOUNT (<?php echo $my_curr ?>)</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $list; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        
        <div class="tab-pane" id="o-tab">
            <div class="row">
                <div class="col-sm-12">
                	<b>Offers Vault</b> are funds received from SusuAI offer promotion, all approved funds are moved to your <b>Voluntary Vault</b>.
                </div>
                <div class="col-sm-12">
                    <div class="widget-inline">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-info mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($offer_pending,2); ?></b></h4>
                                    <p class="text-muted">Pending</p>
                                </div>
                            </div>
        
                            <div class="col-lg-4 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-success mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($offer_approve,2); ?></b></h4>
                                    <p class="text-muted">Approved</p>
                                </div>
                            </div>
        
                            <div class="col-lg-4 col-sm-6">
                                <div class="widget-inline-box text-center b-0">
                                    <h4><i class="text-danger mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($offer_declined,2); ?></b></h4>
                                    <p class="text-muted">Declined</p>
                                </div>
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
            <!--end row -->
            <hr />
            <a href="<?php echo base_url('offer/lists'); ?>" class="btn btn-default btn-md"><i class="mdi mdi-gift"></i> See Transaction Details</a>
            <!-- end row -->
        </div>
        
        <div class="tab-pane" id="v-tab">
            <div class="row">
                <div class="col-sm-12">
                	<b>Voluntary Vault</b> allow you save money for any emergency, you can fund and withdraw anytime.
                </div>
                <div class="col-sm-12">
                    <div class="widget-inline">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-info mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($v_total,2); ?></b></h4>
                                    <p class="text-muted">Total</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-success mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($v_current,2); ?></b></h4>
                                    <p class="text-muted">Balance</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center">
                                    <h4><i class="text-danger mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo $my_curr.number_format($v_withdrawn,2); ?></b></h4>
                                    <p class="text-muted">Withdrawn</p>
                                </div>
                            </div>
        
                            <div class="col-lg-3 col-sm-6">
                                <div class="widget-inline-box text-center b-0">
                                    <a href="<?php echo base_url('vaults/add_fund'); ?>" class="btn btn-success btn-sm">Add Fund</a>
                                    <a href="<?php echo base_url('vaults/withdraw_fund'); ?>" class="btn btn-warning btn-sm">Withdraw</a>
                                </div>
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
            <!--end row -->
            <hr />
            <div class="row">
                <div class="col-xs-12">
                    <div class="m-b-20 table-responsive">
                        <table id="datatable-buttons" class="table table-hover table-striped small">
                            <thead>
                                <tr>
                                    <th><b>DATE</b></th>
                                    <th><b>TYPE</b></th>
                                    <th><b>PURPOSE</b></th>
                                    <th><b>ACTION</b></th>
                                    <th class="text-right"><b>AMOUNT (<?php echo $my_curr ?>)</b></th>
                                    <th><b>STATUS</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $voluntary_list; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>
    
</div>
