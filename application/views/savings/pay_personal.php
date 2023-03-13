<?php
	$active_target = 0;
	$active_save = 0;
	$active_percent = 0;
	$active_alert = 'primary';
	$days = '';
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
			$saving_current = $pers->saving_current;
			$cycle = $pers->cycle;
			$complete = $pers->complete;
			$active = $pers->active;
			$expired = $pers->expired;
			$c_card_id = $pers->card_id;
			$reg_date = $pers->reg_date;
			// check to ensure no saving redundancy
			$diff = (strtotime($pers->saving_next) - strtotime(date('Y-m-d')));
			$days = floor($diff / (60*60*24));
			
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
							$disburse_acc = $acc->acc_name.' <br/><span class="small">['.ucwords($acc->acc_desc).']</span>';
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
            <h4 class="header-title m-t-0">Schedule My Savings 
            	<a href="<?php echo base_url('savings/personal/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="mdi mdi-plus-circle-outline"></i> <span class="hidden-xs">New Plan</span></a>
                <a href="<?php echo base_url('savings/personal/'); ?>" class="btn btn-sm btn-info pull-right"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> <span class="hidden-xs">All Savings</span></a>
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
    	<?php if(!empty($err_msg)){echo $err_msg;} ?>
		<?php echo form_open('savings/personal/p/'.$id, array('class'=>'form-horizontal')); ?>
        <?php if(!empty($pay_auth_url)){ ?>
        <div class="col-xs-12">
        	<iframe frameborder="0" height="500px" width="100%" src="<?php echo $pay_auth_url; ?>"></iframe>
        </div>
        <?php } else { ?>
        <div class="col-xs-12 col-sm-5">
        	<div class="panel bg-twitter social-feed-box">
                <div class="panel-body">
                	<b>PLANNING & DISBURSEMENT</b><hr/>
                	<span class="small">Once completed, will send money to:</span><br />
                    <span style="font-size:x-large;">
                    	<?php 
							if($disburse_acc != '') { 
								echo $disburse_acc;
							} else {
								echo 'No Account Linked<br/>[<a href="'.base_url('savings/personal/v/'.$id).'" class="small">Link Now</a>]';	
							}
						?>
                    </span>
                    <hr />
                    <?php if($cycle > 0){ ?>
                    <span class="small">Starting date:</span><br />
                    <span style="font-size:large;">
                    	<?php echo $save_start; ?>
                 	</span><br /><br />
                    <span class="small">Completion date:</span><br />
                    <span style="font-size:large;">
                    	<?php echo $save_end; ?>
                 	</span>
                    <?php } else { ?>
                    <span class="small">Start My Savings (<?php echo $save_contibute; ?>):</span><br />
                    <span style="font-size:x-large;">
                    	Now <input id="togglevalue" name="togglevalue" checked="checked" data-plugin="switchery" data-color="#3A7EB6" data-switchery="true" type="checkbox" onchange="toggle2();">
                 	</span>
                    <div id="toggle_other" style="display:none;">
                        <br />
                        <span style="font-size:x-large;">
                        	OR Pick Start Date<br />
                            <input class="form-control" placeholder="mm/dd/yyyy" id="datepicker" name="datepicker" type="text">
                        </span>
                    </div>
                    <?php } ?>
                </div>
          	</div>
        </div>
        
        <div class="col-xs-12 col-sm-7">
        	<?php
				if($this->session->userdata('kas_user_phone') == '' || $this->session->userdata('kas_user_email') == ''){
					echo '<div class="alert alert-danger">Your Transaction is protected through Phone/Email, you need to update your profile. <a href="'.base_url('profile').'">Update Now</a></div>';
					$update_profile = true;
				} else {
					$update_profile = false;	
				}
			?>
            <div class="panel bg-default social-feed-box">
                <div class="panel-body">
                	<b>FUNDING CARD</b><hr/>
                    <ul class="nav nav-tabs tabs-bordered">
                        <?php if($cycle > 0){ ?>
                        <li class="active"> <a href="#save_card-1" data-toggle="tab" aria-expanded="true"> <i class="mdi mdi-content-save"></i> Saved </a> </li>
                        <li class=""> <a href="#new_card-1" data-toggle="tab" aria-expanded="fasle"> <i class="mdi mdi-plus"></i> New </a> </li>
                        <?php } else { ?>
                        <li class="active"> <a href="#new_card-1" data-toggle="tab" aria-expanded="true"> <i class="mdi mdi-plus"></i> New </a> </li>
                        <li class=""> <a href="#save_card-1" data-toggle="tab" aria-expanded="false"> <i class="mdi mdi-content-save"></i> Saved </a> </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php if($cycle <= 0){echo 'active';} ?>" id="new_card-1">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <input type="hidden" id="saving_id" name="saving_id" value="<?php echo $id; ?>" />
                                        <input type="hidden" id="amount" name="amount" value="<?php echo $saving; ?>" />
                                        <input type="text" id="card_name" name="card_name" class="form-control" value="<?php echo strtoupper($u_othername).' '.strtoupper($u_lastname); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <input type="text" id="card_no" name="card_no" class="form-control" placeholder="Card Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Exp. Month ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="exp_month" name="exp_month">
                                            <option></option>
                                            <option value="01">01/Jan</option>
                                            <option value="02">02/Feb</option>
                                            <option value="03">03/Mar</option>
                                            <option value="04">04/Apr</option>
                                            <option value="05">05/May</option>
                                            <option value="06">06/Jun</option>
                                            <option value="07">07/Jul</option>
                                            <option value="08">08/Aug</option>
                                            <option value="09">09/Sep</option>
                                            <option value="10">10/Oct</option>
                                            <option value="11">11/Nov</option>
                                            <option value="12">12/Dec</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Exp. Year ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="exp_year" name="exp_year">
                                            <option></option>
                                            <option value="2016">2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <input type="text" id="cvv" name="cvv" class="form-control" placeholder="CVV">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <i class="text-muted small">Save card is securely encrypted for easy recurrent charges, also with OTP usage you are always protected.</i><br />
                                        <b>Save Card:</b> 
                                        <input id="save_card" name="save_card" checked="checked" data-plugin="switchery" data-color="#3A7EB6" data-switchery="true" type="checkbox"> 
                                    </div>
                                </div>
                            </div>
                     	</div>
                        
                        <div class="tab-pane <?php if($cycle > 0){echo 'active';} ?>" id="save_card-1">
                            <?php 
								if(empty($allcard)){
									echo '<h3 class="text-muted">No Saved Card Yet!</h3>';
								} else {
									foreach($allcard as $card){
										$card_id = $card->id;
										$card_name = $card->name;
										$card_no = $card->no;
										$card_no = substr($card_no,0,4).' XXXX XXXX '.substr($card_no, -4);
										
										if($c_card_id == $card_id){$c_act = 'checked="checked"'; $c_bg='#d2ecf7';} else {$c_act = ''; $c_bg='';}
										
										echo '
											<div class="card-box col-xs-12 col-sm-6" style="height:120px; background-color:'.$c_bg.';">
												<div class="radio radio-success">
													<input name="card_id" id="card_id'.$card_id.'" value="'.$card_id.'" type="radio" '.$c_act.'>
													<label for="card_id'.$card_id.'">
														<span class="text-muted">'.$card_no.'</span><br />
														<b>'.$card_name.'</b>
													</label>
												</div>
											</div>
										';	
									}
								}
							?>
                     	</div>
                  	</div>
                    
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <?php if($days > 0){ ?>
                                <span class="text-danger"><b>Sorry!</b> you have to wait till Next Contribution Cycle which is <?php echo $save_next; ?>. We will also notify you.</span>
                                <?php } else { ?>
                                	<?php if($update_profile == true){ ?>
                                    <span class="text-danger"><b>Sorry!</b> you need to update your phone/email to protect and complete your transaction. <a href="<?php echo base_url('profile'); ?>">Update Now</a></span>
                                    <?php } else { ?>
                                	<button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnChangeAccount"><i class="mdi mdi-content-save-all"></i> Save With Card</button>
                                	<?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
          	</div>
        </div>
        <?php } ?>
        <?php echo form_close(); ?>
  	</div>
    
</div>
