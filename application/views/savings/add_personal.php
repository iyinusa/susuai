<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Create Personal Savings <a href="<?php echo base_url('savings/personal'); ?>" class="btn btn-sm btn-info pull-right"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a></h4>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-lg-12">
        	<h6 class="text-muted">Create saving in just few clicks</h6><br />
            <?php if(!empty($err_msg)){echo $err_msg;} ?>
        </div>
        
        <?php if($done_savings == FALSE){ ?>
        <div class="col-lg-12">
		<?php echo form_open('savings/personal/add', array('class'=>'form-horizontal')); ?>
                <?php if($this->session->userdata('kas_user_country_currency') == ''){ ?>
                <div class="alert alert-warning">
                	<h3>Change Currency</h3>
                    Change country to change your Currency Format, else you will continue to have Nigerian Currency Format. This changes is only made once, and it's not reversable.<br /><br />
                    <div class="row">
                    	<div class="col-xs-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <?php
                                            $country_list = '';
                                            if(!empty($allcountry)){
                                                foreach($allcountry as $ct){
                                                    $country_list .= '<option value="'.$ct->id.'">'.$ct->name.'</option>';	
                                                }
                                            }
                                        ?>
                                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Country ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="country_id" name="country_id">
                                            <option></option>
                                            <?php echo $country_list; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <button class="btn btn-success waves-effect waves-light w-md" type="submit" name="btnCountry"><i class="mdi mdi-content-save-all"></i> Save Country</button>
                                    </div>
                                </div>
                            </div>
                    	</div>
                    </div>
                </div>
                <?php } ?>
                
                <div class="col-xs-12 col-sm-7">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <label for="name">Name Your Saving (e.g. House Rent)</label>
                            <input class="form-control input-lg" type="text" id="name" name="name" placeholder="Your saving name" value="<?php echo $reset ? '' : set_value('name'); ?>" oninput="ps_cal();" <?php if($this->session->userdata('kas_user_country_currency') != ''){echo 'required';} ?>>
                        </div>
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-5">
                	<div class="form-group">
                        <div class="col-xs-12">
                            <label for="target">Your Target (e.g. 250000)</label>
                            <input class="form-control input-lg text-center" type="text" id="target" name="target" oninput="ps_cal();" placeholder="Your saving price target" value="<?php echo $reset ? '' : set_value('target'); ?>" <?php if($this->session->userdata('kas_user_country_currency') != ''){echo 'required';} ?>>
                        </div>
                    </div>
              	</div>
                
                <div class="col-xs-12 col-sm-7">
                    <div class="form-group m-b-20">
                        <div class="col-xs-12">
                            <input type="hidden" id="contribute_amt" name="contribute_amt" />
                            <label for="contribute">Savings Interval</label><br />
                            <div class="radio radio-info radio-inline" style="font-size:large;">
                                <input id="monthly" value="Monthly" name="contribute" type="radio" oninput="ps_cal();" checked="checked">
                                <label for="monthly"> Monthly </label>
                            </div>
                            <div class="radio radio-info radio-inline" style="font-size:large;">
                                <input id="weekly" value="Weekly" name="contribute" type="radio" oninput="ps_cal();">
                                <label for="weekly"> Weekly </label>
                            </div>
                            <div class="radio radio-info radio-inline" style="font-size:large;">
                                <input id="daily" value="Daily" name="contribute" type="radio" class="form-control" oninput="ps_cal();">
                                <label for="daily"> Daily </label>
                            </div>
                        </div>
                    </div>
             	</div>
                
              	<div class="col-xs-12 col-sm-5">
                    <div class="form-group m-b-20">
                        <div class="col-xs-12">
                            <label for="duration">Target Duration (e.g. 7)</label>
                            <input class="form-control input-lg text-center" type="text" id="duration" name="duration" oninput="ps_cal();" placeholder="Complete savings" value="<?php echo $reset ? '' : set_value('duration'); ?>" <?php if($this->session->userdata('kas_user_country_currency') != ''){echo 'required';} ?>>
                        </div>
                    </div>
             	</div>
                
                <div id="ps_msg"></div>
                
                <div class="col-xs-12">
                	<div class="form-group">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnSavings"><i class="mdi mdi-content-save-all"></i> Look's Good, Proceed</button>
                        </div>
                	</div>
                </div>
            
        <?php echo form_close(); ?>
        </div>
        <?php } ?>
        
        <?php if($done_savings == TRUE){ ?>
        <div class="col-lg-12">
		<?php echo form_open('savings/personal/add', array('class'=>'form-horizontal')); ?>
        	<div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <div class="col-xs-12">
                        <input type="hidden" id="p_save_id" name="p_save_id" value="<?php echo $p_save_id; ?>">
                        <table class="table table table-hover m-0">
                        	<thead>
                            	<tr>
                                	<th colspan="2" class="text-center text-primary"><b>YOUR NEW SAVINGS PLAN</b></th>
                                </tr>
                            </thead>
                            <tbody>
                            	<tr>
                                	<td width="70px" class="text-right"><b>Savings:</b></td>
                                    <td><?php echo $get_name; ?></td>
                                </tr>
                                <tr>
                                	<td class="text-right"><b>Target:</b></td>
                                    <td><?php echo $get_target; ?></td>
                                </tr>
                                <tr>
                                	<td class="text-right"><b>Duration:</b></td>
                                    <td><?php echo $get_duration; ?></td>
                                </tr>
                                <tr>
                                	<td class="text-right"><b>Contribution:</b></td>
                                    <td><?php echo $get_contibute; ?></td>
                                </tr>
                            </tbody>
                        </table><hr />
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <div class="col-xs-12">
                        <?php
							$all_acc = '';
							if(!empty($allacc)){
								foreach($allacc as $acc){
									$all_acc .= '<option value="'.$acc->id.'">'.$acc->acc_name.'</option>';
								}
							}
						?>
                        <label for="acc_id">Savings Completed! Move Funds To Account</label>
                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="acc_id" name="acc_id">
                            <option></option>
                            <?php echo $all_acc; ?>
                       	</select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 text-center text-muted">
                        <h5>== OR ==</h5>
                    </div>
                </div>
                
                <div class="form-group">
                	<div class="col-xs-12">
                        <label for="vacc_no">New Account</label>
                        <input id="vacc_no" name="acc_no" class="form-control" placeholder="Account number" type="text">
                    </div>
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
                    <div class="col-xs-12">
                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="vbank" name="bank" onchange="get_verify_acc();">
                            <option></option>
                            <?php echo $all_bank; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                	<div class="col-xs-12">
                    	<input type="text" id="vacc_name" name="acc_name" class="form-control" readonly="readonly">
                    </div>
                </div>
                <div class="form-group">
                	<div class="col-xs-12">
                    	<input type="text" name="acc_desc" class="form-control" placeholder="Describe Account (i.e. My Landlord)">
                    </div>
                </div>
                
                <div class="col-xs-12">
                	<div class="form-group">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-primary waves-effect waves-light w-md" type="submit" name="btnAccount"><i class="mdi mdi-content-save-all"></i> Assign Account Now</button> 
                            <a class="btn btn-default" href="<?php echo base_url('savings/personal'); ?>">Assign Later</a>
                        </div>
                	</div>
                </div>
                
            </div>
        <?php echo form_close(); ?>
        </div>
        <?php } ?>
    </div>
</div>
