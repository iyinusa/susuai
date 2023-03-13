<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Withdraw Fund</h4>
            <p class="text-muted m-b-30">
                Withdraw fund for your emergency use.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<?php if(!empty($err_msg)){echo $err_msg;} ?>
		<?php echo form_open('vaults/withdraw_fund', array('class'=>'form-horizontal')); ?>
        <div class="col-xs-12">
        	<h3><span class="text-muted">AVAILABLE BALANCE:</span> <span class="text-success"><?php echo $my_curr.number_format($vv_balance,2); ?></span></h3><br />
        </div>
        
        <div class="col-xs-12 col-sm-7">
        	<div class="panel bg-default social-feed-box">
            	<?php if($confirm_fund == true) { ?>
                <div class="panel-body">
					<b>PLEASE CONFIRM</b><hr />
                    <div class="col-xs-12">
                        <input type="hidden" name="c_bank" value="<?php echo $c_bank; ?>" />
                        <input type="hidden" name="c_bank_name" value="<?php echo $c_bank_name; ?>" />
                        <input type="hidden" name="c_bank_code" value="<?php echo $c_bank_code; ?>" />
                        <input type="hidden" name="c_acc_no" value="<?php echo $c_acc_no; ?>" />
                        <input type="hidden" name="c_acc_name" value="<?php echo $c_acc_name; ?>" />
                        <input type="hidden" name="c_amount" value="<?php echo $c_amount; ?>" />
                        <div class="form-group">
                            <div class="col-xs-12">
                                <h4>SEND <b class="text-lg"><?php echo $my_curr.number_format((float)$c_amount,2); ?></b> TO:</h4><br/>
                                <h5><?php echo $c_acc_name; ?></h5>
                                <span class="text-muted">
                                	<?php echo $c_acc_no; ?><br />
                                    <?php echo $c_bank_name; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-12">
                            	<button class="btn btn-success waves-effect waves-light w-md" type="submit" name="btnConfirm"><i class="mdi mdi-content-save-all"></i> Confirm</button>&nbsp;
                                <a href="<?php echo base_url('vaults/withdraw_fund'); ?>" class="btn btn-default waves-effect waves-light w-md"><i class="mdi mdi-close"></i> Cancel</a>
                            </div> 
                        </div>
                    </div>
              	</div>
                <?php } else { ?>
                <div class="panel-body">
					<b>SPECIFY AMOUNT (<?php echo $my_curr; ?>)</b><hr />
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="amount" name="amount" class="form-control" placeholder="20000" type="text">
                                <br />
                            </div>
                        </div>
                    </div>
                    
                    <b>SEND TO SAVED ACCOUNT</b><hr />
					<?php
                        $all_acc = '';
                        if(!empty($allacc)){
                            foreach($allacc as $acc){
                                // get bank name
								$bank_name = '';
								$getbank = $this->Crud->read_single('id', $acc->bank_id, 'ka_bank');
								if(!empty($getbank)) {
									foreach($getbank as $bk) {
										$bank_name = $bk->name;	
									}
								}
								$all_acc .= '<option value="'.$acc->id.'">'.$acc->acc_name.' ['.$acc->acc_no.' - '.$bank_name.']</option>';
                            }
                        }
                    ?>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="acc_id" name="acc_id">
                                    <option></option>
                                    <?php echo $all_acc; ?>
                                </select>
                                <br /><br />
                            </div>
                        </div>
                    </div>
                    
                    <b>OR SPECIFY NEW ACCOUNT</b> <a id="togglebtn" href="javascript:;" onclick="toggle();" class="text-danger small">- Change</a><hr />
                    <div id="toggle_me" style="display:none;">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input id="vacc_no" name="acc_no" class="form-control" placeholder="Account number" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
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
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input type="text" id="vacc_name" name="acc_name" class="form-control" readonly="readonly">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input type="text" name="acc_desc" class="form-control" placeholder="Describe Account (i.e. My Landlord)">
                                </div>
                            </div>
                        </div>
                  	</div>
                    
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-12">
                            	<button class="btn btn-primary waves-effect waves-light w-md" type="submit"><i class="mdi mdi-content-save-all"></i> Withdraw To Account Now</button>
                            </div> 
                        </div>
                    </div>
              	</div>
                <?php } ?>
           	</div>
        </div>
        <?php echo form_close(); ?>
  	</div>
    
</div>
