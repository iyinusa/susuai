<?php
	$list = '';
	if(!empty($alloffer)){
		foreach($alloffer as $of){
			$id = $of->id;
			$com_id = $of->com_id;
			$saving_id = $of->saving_id;
			$interest = $of->interest;
			$offer_no = $of->offer_no;
			$product_link = $of->product_link;
			$status = $of->status;
			$reason = $of->reasons;
			$reg_date = $of->reg_date;
			
			if($status == 'Approved') {
				$alert = 'success';
			} else if($status == 'Pending') {
				$alert = 'info';
			} else if($status == 'Withdrawn') {
				$alert = 'warning';
			} else if($status == 'Declined') {
				$alert = 'danger';
			}
			
			// get savings
			$savings_name = '';
			$getsave = $this->Crud->read_single('id', $saving_id, 'ka_personal');
			if(!empty($getsave)){
				foreach($getsave as $save){
					$savings_name = $save->name.' ('.$my_curr.number_format((float)$save->target,2).')';
				}
			}
			
			// get commission
			$comm_name = '';
			$getcomm = $this->Crud->read_single('id', $com_id, 'ka_offer_commission');
			if(!empty($getcomm)){
				foreach($getcomm as $com){
					$comm_name = $com->name.' ('.$com->com.'%)';
				}
			}
			
			// user can only delete offer is still pending
			if($status == 'Pending') {
				$del_btn = '<a class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Delete Record" href="'.base_url('offer/lists/d/'.$id).'"><i class="mdi mdi-close"></i></a>';
			} else {
				$del_btn = '';	
			}
			
			$list .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.date('d M, Y', strtotime($reg_date)).'</td>
					<td>'.$offer_no.'</td>
					<td>'.$savings_name.'</td>
					<td>'.$comm_name.'</td>
					<td>'.$my_curr.number_format($interest,2).'</td>
					<td>'.$status.'</td>
					<td>
						'.$del_btn.'
					</td>
				</tr>
			';
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Offers</h4>
            <p class="text-muted m-b-30">
                All subscribed offers. Click "<b>Subscribe Now</b>" to start enjoying our offers
                <?php if($param1 == ''){ ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('offer/lists/a'); ?>"><i class="mdi mdi-plus-circle-outline"></i> Subcribe Now</a>
                <?php } else { ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('offer/lists'); ?>"><i class="mdi mdi-close-circle-outline"></i> Cancel</a>
                <?php } ?>
            </p>
        </div>
    </div>
    
    <?php if(!empty($err_msg)){echo $err_msg;} ?>
    
    <?php if($param1 == 'a'){ // add ?>
    <?php
		$tos = '
			<b>TERMS & CONDITIONS</b><br /><br />
			Please note that buy clicking "Save Record" means you understand and agree to below Terms and Conditions:<br /><br/>
			<p>
				<ul>
					<li>Offer linked to savings can not be changed, but you can choose to delete it only when in "Pending" Status.</li>
					<li>This offer is provided to you by SusuAI, not Jumia nor any third-party.</li>
					<li>You must complete linked savings, and must click on the link provided to you by SusuAI on your Savings completion for the purchase of your Jumia Order, else you will not receive Money Back.</li>
					<li>Agree that money payback within 30 days will only apply if and on if you follow SusuAI procedure of Purchase and Jumia order was successful and no returned.</li>
					<li>SusuAI will not be responsible for any Ordered Item faulty from Jumia, it\'s all fully between you and Jumia. We only encourage you to save, purchase and get certain percentage money back from SusuAI, not from Jumia.</li>
					<li>You must copy and paste correct product link from Jumia website into "Product Link" textbox, and must select appropriate Category (select "Others" if you are not sure), else you will not receive a Money Back.</li>
					<li>If you need any further clarification, kindly contact webmaster@susu-ai.com.</li>
				</ul>
			</p>
		';
		echo $this->Crud->msg('warning', $tos);
	?>
    
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('offer/lists/a', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <?php
						$partner_list = '';
						$offer_list = '';
						if(!empty($allpartner)){
							foreach($allpartner as $partner){
								$c_name = $this->Crud->country_data($partner->country_id, 'name');
								$partner_list .= '<option value="'.$partner->id.'">'.$partner->name.' - '.$c_name.'</option>';
								
								// load offer
								$offer_list = '';
								$getoffer = $this->Crud->read_single('partner_id', $partner->id, 'ka_offer_commission');
								if(!empty($getoffer)) {
									foreach($getoffer as $goffer){
										$offer_list .= '<option value="'.$goffer->id.'">'.$goffer->name.'</option>';
									}
								}
							}
						}
					?>
                    <label for="role">Offer Group</label>
                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="partner_id" name="partner_id" required>
                        <!--<option></option>-->
                        <?php echo $partner_list; ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <label for="role">Offer Category</label>
                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="com_id" name="com_id" required>
                        <option></option>
                        <?php echo $offer_list; ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <label for="role">Product Link <small class="text-muted">(copy and paste from Jumia) - <a class="text-danger" href="javascript:;" data-toggle="modal" data-target="#jumia_sample" title="View Sample"><i class="ti-eye"></i> See Sample</a></small></label>
                    <input type="text" id="product_link" name="product_link" class="form-control" required />
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <?php
						$personal_list = '';
						if(!empty($allpersonal)){
							foreach($allpersonal as $pers){
								$personal_list .= '<option value="'.$pers->id.'">'.ucwords($pers->name).'</option>';
							}
						}
					?>
                    <label for="role">Link Savings</label>
                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="saving_id" name="saving_id" required>
                        <option></option>
                        <?php echo $personal_list; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-xs-12 text-right">
                <div class="form-group col-xs-12">
                    <button class="btn btn-primary waves-effect waves-light w-md" type="submit"><i class="mdi mdi-content-save-all"></i> Save Record</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
  	</div>
    <hr />
    <?php } ?>
    
    <?php if($param1 == 'd'){ // delete ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('offer/lists/d', array('class'=>'form-horizontal')); ?>
            <div class="form-group col-xs-12">
                <input type="hidden" name="d_offer_id" value="<?php if(!empty($d_id)){echo $d_id;} ?>" />
                <h4>Are you sure you want to DELETE Offer, you will no get your money back again?</h4>
            </div>
            <div class="col-xs-12">
                <div class="form-group col-xs-12">
                    <button class="btn btn-danger waves-effect waves-light w-md" type="submit" name="btnYes"><i class="mdi mdi-check"></i> Yes</button> 
                    <button class="btn btn-default waves-effect waves-light w-md" type="submit" name="btnNo"><i class="mdi mdi-close"></i> No</button>
                </div>
            </div>
       	</div>
   	</div>
    <hr />
    <?php } ?>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="datatable-buttons" class="table table-hover table-striped small">
                    <thead>
                        <tr>
                            <th width="50"><b>DATE</b></th>
                            <th width="80"><b>OFFER ID</b></th>
                            <th><b>SAVINGS</b></th>
                            <th><b>OFFER</b></th>
                            <th><b>MONEY BACK</b></th>
                            <th><b>STATUS</b></th>
                            <th width="50"><b></b></th>
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
    
    <div id="jumia_sample" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelj" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabelj">Link Sample</h4>
                </div>
                <div class="modal-body">
                    <h4>Visit Jumia website and search your product, on the product page, copy the link in Address Bar as illustrated below then paste it in "Product Link" text box.</h4>
                    <div>
                    	<img alt="" src="<?php echo base_url('assets/images/jumia_sample.jpg'); ?>" style="max-width:100%;" />
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-default" data-dismiss="modal"><i class="ti-close"></i> Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
