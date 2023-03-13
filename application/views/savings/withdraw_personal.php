<?php
	$active_target = 0;
	$active_save = 0;
	$active_percent = 0;
	$active_alert = 'primary';
	$list = '';
	if(!empty($allpersonal)){
		foreach($allpersonal as $pers){
			$id = $pers->id;
			$name = $pers->name;
			$target = $pers->target;
			$type = $pers->type;
			$duration = $pers->duration;
			$saving = $pers->saving;
			$saving_start = $pers->saving_start;
			$saving_end = $pers->saving_end;
			$saving_next = $pers->saving_next;
			$cycle = $pers->cycle;
			$complete = $pers->complete;
			$active = $pers->active;
			$expired = $pers->expired;
			$disbused = $pers->disbursed;
			$reg_date = $pers->reg_date;
			
			if($cycle > 0){$save_start = date('d M, Y', strtotime($saving_start));} else {$save_start = 'Not Started';}
			if($cycle > 0){$save_end = date('d M, Y', strtotime($saving_end));} else {$save_end = 'Not Started';}
			if($cycle > 0){$save_next = date('d M, Y', strtotime($saving_next));} else {$save_next = 'Not Started';}
			
			if($complete == 1){$status = 'Completed';} else {
				if($expired == 1) {$status = 'Incompleted';} else {$status = 'In-progress';}	
			}
			
			if($type == 'Monthly'){
				$save_type = 'Month';	
			} else if($type == 'Weekly'){
				$save_type = 'Week';	
			} else {
				$save_type = 'Day';
			}
			
			if($duration > 1){$save_type.='s';}
			
			$save_duration = $duration.' '.$save_type;
			$save_contibute = $my_curr.number_format((float)$saving,2).' '.$type;
			
			// get linked account
			$disburse_acc = '';
			$acc_id = '';
			$getacclink = $this->Crud->read_single('saving_id', $id, 'ka_account_link');
			if(!empty($getacclink)){
				foreach($getacclink as $acclink){
					$acc_id =  $acclink->acc_id;
					$getacc = $this->Crud->read_single('id', $acclink->acc_id, 'ka_account');	
					if(!empty($getacc)){
						foreach($getacc as $acc){
							$disburse_acc = $acc->acc_name.' ['.ucwords($acc->acc_desc).']';
						}
					}
				}
			}
			
			// get contributions
			$saves = 0;
			$getcontr = $this->Crud->read_single('saving_id', $pers->id, 'ka_contribute');
			if(!empty($getcontr)){
				foreach($getcontr as $contr){
					$saves = $saves + (float)$contr->amt;
					
					$list .= '
						<tr>
							<td>'.date('d M, Y', strtotime($contr->reg_date)).'</td>
							<td>'.number_format((float)$contr->amt,2).'</td>
							<td>'.$contr->type.'</td>
						</tr>
					';	
				}
			}
			
			if($active == 1){
				$status_text = 'Active';
				$status_alart = 'success';
			} else {
				if($expired == 1) {
					if($complete == 1) {
						$status_text = 'Completed';
						$status_alart = 'success';
					} else {
						$status_text = 'Expired';
						$status_alart = 'danger';
					}
				} else {
					$status_text = 'Inactive';
					$status_alart = 'warning';
				}
			}
			
			$active_save = $active_save + $saves;
			$active_target = $active_target + (float)$target;
			
			$charge = $this->Crud->commission((float)$target);
			$cal_charge = ($charge / 100) * (float)$target;
			$service_charge = $charge.'% - '.$my_curr.number_format($cal_charge,2);
			
			// check if linked to offer
			$offer_img = 'assets/images/jumia_logo.png';
			$offer_name = '';
			$offer_com = 0;
			$interest = 0;
			$offer_link = '';
			$offer_status = '';
			$offer_reason = '';
			$checkoffer = $this->Crud->read2('user_id', $pers->user_id, 'saving_id', $id, 'ka_offer');
			if(!empty($checkoffer)){
				foreach($checkoffer as $offer){
					$com_id = $offer->com_id;
					$interest = $offer->interest;
					$offer_link = $offer->offer_link;
					$offer_status = $offer->status;
					$offer_reason = $offer->reasons;
					
					// get commission
					$comm = $this->Crud->read_single('id', $com_id, 'ka_offer_commission');	
					if(!empty($comm)){
						foreach($comm as $co) {
							$offer_com = $co->com;	
							$offer_name = $co->name;	
						}
					}
				}
			}
			
		}
		
		// calculated disburse amount
		if($cycle < $duration){
			$disburse_amount = $active_save - ($active_save * 0.05);	
		} else {$disburse_amount = $active_save;}
		
		if($active_target > 0){$active_percent  = ($active_save / $active_target) * 100;}
		
		if($active_percent >= 0 && $active_percent < 20){ $active_alert = 'danger';	
		} else if($active_percent < 40 && $active_percent >= 20){ $active_alert = 'warning';	
		} else if($active_percent < 60 && $active_percent >= 40){ $active_alert = 'info';	
		} else if($active_percent < 80 && $active_percent >= 60){ $active_alert = 'primary';	
		} else if($active_percent > 80) { $active_alert = 'success'; }
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Withdraw/Disburse Savings 
            	<a href="<?php echo base_url('savings/personal/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="mdi mdi-plus-circle-outline"></i> New</a>
                <a href="<?php echo base_url('savings/personal/'); ?>" class="btn btn-sm btn-info pull-right"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> <span class="hidden-xs">All</span></a>
            </h4>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-lg-12">
            <div class="card-box">
                <span class="btn btn-sm btn-<?php echo $status_alart; ?> pull-right"><i class="ti-info"></i> <?php echo $status_text; ?></span>
                <h4 class="text-muted m-t-0 text-uppercase"><?php echo $name; ?></h4><br/>
                <h4 class="m-b-20 row">
                    <span class="pull-left text-<?php echo $active_alert; ?>"><?php echo $my_curr.number_format($active_save,2); ?></span>
                    <span class="pull-right text-success"><?php echo $my_curr.number_format($active_target,2); ?></span>
                </h4>
                <div class="progress progress-sm m-0">
                    <div class="progress-bar progress-bar-<?php echo $active_alert; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo number_format($active_percent); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo number_format($active_percent); ?>%;">
                        <span class="sr-only"><?php echo number_format($active_percent); ?>% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
    	<? if(!empty($err_msg)){echo $err_msg;} ?>
		<?php echo form_open('savings/personal/w/'.$id, array('class'=>'form-horizontal')); ?>
        <div class="col-xs-12">
        	<h3>
            	<span class="hidden-xs"><?php echo strtoupper($name); ?> SAVINGS</span> WITHDRAW/DISBURSE
            </h3><hr />
            
            <input type="hidden" name="acc_id" value="<?php echo $acc_id; ?>" />
            <?php
				// protect stealth acc fraud
				$sreg_acc = array('kas_disacc'=>$acc_id);
				$this->session->set_userdata($sreg_acc);
			?>
            
            <h4 class="col-xs-12">
            	<?php if($disburse_acc != '') { ?>
					Disburse To: <?php echo $disburse_acc; ?> <b><i class="mdi mdi-bank"></i> <a href="<?php echo base_url('savings/personal/v/'.$id); ?>" class="text-danger small">- Change Account</a></b>
                <?php } else { ?>
                    Disburse To: NO ACCOUNT <b><i class="mdi mdi-bank"></i> <a href="<?php echo base_url('savings/personal/v/'.$id); ?>" class="text-danger small">- Assign Account</a>
                <?php } ?>
            </h4><br /><br />
            
            <div class="card-box col-xs-12 col-sm-6">
            	<?php if($cycle < $duration){ ?>
                    You are yet to complete savings, to encourage you to so see below disbursement terms:
                    <br /><br />
                    <b class="text-primary">Disburse/Withdraw on Completion:</b><br /><?php echo $my_curr.number_format((float)$active_target,2); ?> <i class="small text-muted">(Charges 0% of Current Savings)</i><br /><br />
                    
                    <b class="text-danger">Disburse/Withdraw Now:</b> <br /><?php echo $my_curr.number_format((float)$disburse_amount,2); ?> <i class="small text-muted">(Charges 5% of Current Savings)</i><br /><br />
                    
                    <?php
						// protect stealth amount fraud
						$reg_sess = array('kas_disamt'=>$disburse_amount);
						$this->session->set_userdata($reg_sess);
					?>
                    <input type="hidden" name="amount" value="<?php echo $disburse_amount; ?>" />
                    
                    <?php if($disbused == 0){ ?>
                    <button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnDisburse"><i class="mdi mdi-content-save-all"></i> Request <?php echo $my_curr.number_format((float)$disburse_amount,2); ?> To Account</button> 
                    <?php } else { ?>
                    <h3 class="text-danger">Disbursed Processed</h3>
                    <?php } ?>
                <?php } else { ?>
                    You have completed your savings and want to disburse below:
                    <br /><br />
                    <b class="text-primary">Disburse/Withdraw:</b> <br /><?php echo $my_curr.number_format((float)$active_target,2); ?><br /><br />
                    <?php
						// protect stealth amount fraud
						$reg_sess = array('kas_disamt'=>$active_target);
						$this->session->set_userdata($reg_sess);
					?>
                    <input type="hidden" name="amount" value="<?php echo $active_target; ?>" />
                    
                    <?php if($disbused == 0){ ?>
                    <button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnDisburse"><i class="mdi mdi-content-save-all"></i> Request <?php echo $my_curr.number_format((float)$active_target,2); ?> To Account</button> 
                    <?php } else { ?>
                    <h3 class="text-danger">Disbursed Processed</h3>
                    <?php } ?>
                <?php } ?>
            </div>
            
            <?php if($offer_status != '') { ?>
            <div class="box col-xs-12 col-sm-6">
            	<h4>OFFER <small>[<?php echo $offer_status; ?>]</small></h4>
                <hr />
                <img alt="" src="<?php echo base_url($offer_img); ?>" style="max-width:100%;" /><br /><br />
                <?php
					if($offer_name != '') {
						echo '<h4 class="text-primary">'.$offer_name.' ['.$offer_com.'% - '.$my_curr.number_format((float)$interest,2).']<br/><small class="text-muted">Money Back within 30 days!</small></h4>';	
					}
					
					if($offer_status == 'Pending') {
						echo '<h5>Keep eyes here for your Offer Link once savings is completed, if not clicked you will NOT get money back.</h5>';
					} else if($offer_status == 'Approved') {
						echo '<a href="'.$offer_link.'" target="_blank" class="btn btn-success"><i class="mdi mdi-gift"></i> CLAIM THIS OFFER</a>';
					} else {
						echo '<b>Reasons:</b> '.$offer_reason;
					}
				?>
            </div>
            <?php } ?>
        </div>
        
        <?php echo form_close(); ?>
    </div>
    
</div>
