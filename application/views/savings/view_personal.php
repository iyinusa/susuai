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
			$disbursed = $pers->disbursed;
			$active = $pers->active;
			$expired = $pers->expired;
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
			$getacclink = $this->Crud->read_single('saving_id', $id, 'ka_account_link');
			if(!empty($getacclink)){
				foreach($getacclink as $acclink){
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
		}
		
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
            <h4 class="header-title m-t-0">Personal Savings 
            	<a href="<?php echo base_url('savings/personal/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="mdi mdi-plus-circle-outline"></i> New Plan</a>
                <a href="<?php echo base_url('savings/personal/'); ?>" class="btn btn-sm btn-info pull-right"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> <span class="hidden-xs">All Savings</span></a>
            </h4>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-lg-12">
            <div class="card-box">
                <span class="btn btn-sm btn-<?php echo $status_alart; ?> pull-right"><i class="ti-info"></i> <?php echo $status_text; ?></span>
                <?php if($cycle > 0){ ?>
                	<?php if($disbursed == 0){ ?>
                	<a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('savings/personal/w/'.$id); ?>"><i class="ti-wallet"></i> Withdraw/Offer</a>
                    <?php } else { ?>
                    <a class="btn btn-sm btn-primary pull-right" href="javascript:;"><i class="ti-wallet"></i> Disbursed</a>
                    <?php } ?>
                <?php } ?>
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
    	<?php if(!empty($err_msg)){echo $err_msg;} ?>
		<?php echo form_open('savings/personal/v/'.$id, array('class'=>'form-horizontal')); ?>
        <div class="col-xs-12 col-sm-6">
        	<table class="table table table-hover m-0 small">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center text-primary"><b><?php echo strtoupper($name); ?> SAVINGS DETAILS</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-right"><b>Duration:</b></td>
                        <td><?php echo $save_duration; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Contribution:</b></td>
                        <td><?php echo $save_contibute; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Cycle:</b></td>
                        <td><?php echo $cycle.' of '.$duration; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Status:</b></td>
                        <td><?php echo $status; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Start Date:</b></td>
                        <td><?php echo $save_start; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Next Contribution:</b></td>
                        <td><?php echo $save_next; ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Completion Date:</b></td>
                        <td><?php echo $save_end; ?></td>
                    </tr>
                    <!--<tr>
                        <td class="text-right"><b>Service Charge:</b><br/><i class="small text-muted">During disbursement</i></td>
                        <td><?php echo $service_charge; ?></td>
                    </tr>-->
                    <tr>
                        <td class="text-right"><b>To Disburse Account:</b></td>
                        <td>
							<?php if($disburse_acc != '') { ?>
                            	<?php echo $disburse_acc; ?> <b><a id="togglebtn" href="javascript:;" onclick="toggle();" class="text-danger small">- Change</a></b>
                            <?php } else { ?>
                            	<b><a id="togglebtn" href="javascript:;" onclick="toggle();" class="text-danger small">- Change</a>
                            <?php } ?>
                            <div id="toggle_me" class="col-lg-12" style="display:none;">
                                <hr />
                                <h4>== OR ==</h4>
                                <input type="hidden" name="p_save_id" value="<?php echo $id; ?>" />
                                <?php
                                    $all_acc = '';
                                    if(!empty($allacc)){
                                        foreach($allacc as $acc){
                                            $all_acc .= '<option value="'.$acc->id.'">'.$acc->acc_name.'</option>';
                                        }
                                    }
                                ?>
                                <div class="form-group">
                                    <label for="acc_id">Savings Completed! Move Funds To Account</label>
                                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="acc_id" name="acc_id">
                                        <option></option>
                                        <?php echo $all_acc; ?>
                                    </select>
                                </div>
                                <h4>== OR ==</h4>
                                <div class="form-group">
                                    <label for="vacc_no">New Account</label>
                                    <input id="vacc_no" name="acc_no" class="form-control" placeholder="Account number" type="text">
                                </div>
                                <div class="form-group">
                                	<?php
										$all_bank = '';
										if(!empty($allbank)){
											foreach($allbank as $bank){
												$all_bank .= '<option value="'.$bank->id.'">'.$bank->name.'</option>';
											}
										}
									?>
                                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="vbank" name="bank" onchange="get_verify_acc();">
										<option></option>
										<?php echo $all_bank; ?>
									</select>
                                </div>
                                <div class="form-group">
                                    <input type="text" id="vacc_name" name="acc_name" class="form-control" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="acc_desc" class="form-control" placeholder="Describe Account (i.e. My Landlord)">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnChangeAccount"><i class="mdi mdi-content-save-all"></i> Change Account Now</button> 
                                </div>
                            </div>
                            
                            
                        </td>
                    </tr>
                    <?php if($cycle == 0){ ?>
                    <tr>
                        <td class="text-right"><b>Plan My Savings:</b></td>
                        <td>
							<a class="btn btn-success btn-sm waves-effect waves-light w-md" href="<?php echo base_url('savings/personal/p/'.$id); ?>"><i class="mdi mdi-wallet"></i> Start Saving</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table><hr />
        </div>
        
        <div class="col-xs-12 col-sm-6">
        	<table class="table table table-hover m-0 small">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center text-primary"><b>CONTRIBUTION DETAILS</b></th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th><b>DATE</b></th>
                        <th><b>AMOUNT (<?php echo $my_curr ?>)</b></th>
                        <th><b>TYPE</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $list; ?>
            	</tbody>
           	</table>
        </div>
        <?php echo form_close(); ?>
    </div>
    
</div>
