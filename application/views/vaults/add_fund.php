<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Fund Voluntary Vault</h4>
            <p class="text-muted m-b-30">
                Fund your Voluntary Vault for emergency.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<? if(!empty($err_msg)){echo $err_msg;} ?>
		<?php echo form_open('vaults/add_fund', array('class'=>'form-horizontal')); ?>
        <?php if(!empty($pay_auth_url)){ ?>
        <div class="col-xs-12">
        	<iframe frameborder="0" height="500px" width="100%" src="<?php echo $pay_auth_url; ?>"></iframe>
        </div>
        <?php } else { ?>
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
                	<b>AMOUNT (<?php echo $my_curr; ?>)</b><hr/>
                    <input type="text" id="amount" name="amount" class="form-control" placeholder="20000">
                    <br /><br />
                    
                    <b>FUNDING CARD</b><hr/>
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="active"> <a href="#new_card-1" data-toggle="tab" aria-expanded="true"> <i class="mdi mdi-plus"></i> New </a> </li>
                        <li class=""> <a href="#save_card-1" data-toggle="tab" aria-expanded="false"> <i class="mdi mdi-content-save"></i> Saved </a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="new_card-1">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <div class="col-xs-12">
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
                        
                        <div class="tab-pane" id="save_card-1">
                            <?php 
								if(empty($allcard)){
									echo '<h3 class="text-muted">No Saved Card Yet!</h3>';
								} else {
									foreach($allcard as $card){
										$card_id = $card->id;
										$card_name = $card->name;
										$card_no = $card->no;
										$card_no = substr($card_no,0,4).' XXXX XXXX '.substr($card_no, -4);
										
										echo '
											<div class="card-box col-xs-12 col-sm-6" style="height:120px;">
												<div class="radio radio-success">
													<input name="card_id" id="card_id'.$card_id.'" value="'.$card_id.'" type="radio">
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
                                <?php if($update_profile == true){ ?>
                                <span class="text-danger"><b>Sorry!</b> you need to update your phone/email to protect and complete your transaction. <a href="<?php echo base_url('profile'); ?>">Update Now</a></span>
                                <?php } else { ?>
                                <button class="btn btn-primary waves-effect waves-light w-md" type="submit"><i class="mdi mdi-content-save-all"></i> Fund With Card</button>
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
    
    <hr />
    
    <?php echo $testresp; ?>
    
</div>
