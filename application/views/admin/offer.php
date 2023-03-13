<?php
	$list = '';
	if(!empty($allpartner)){
		foreach($allpartner as $partner){
			$id = $partner->id;
			$country_id = $partner->country_id;
			$name = $partner->name;
			
			// get country
			$country_name = '';
			$getcountry = $this->Crud->read_single('id', $country_id, 'ka_country');
			if(!empty($getcountry)){
				foreach($getcountry as $country){
					$country_name = $country->name;
				}
			}
			
			$list .= '
				<tr>
					<td width="150px">'.$country_name.'</td>
					<td>'.$name.'</td>
					<td>
						<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/offer/a_partner/'.$id).'"><i class="mdi mdi-pencil"></i></a>
						<a class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Delete Record" href="'.base_url('admin/offer/d_partner/'.$id).'"><i class="mdi mdi-close"></i></a>
					</td>
				</tr>
			';
		}
	}
	
	// list offer commissions
	$list_com = '';
	if(!empty($allcom)){
		foreach($allcom as $com){
			$id = $com->id;
			$partner_id = $com->partner_id;
			$name = $com->name;
			$com = $com->com;
			
			// get partner
			$partner_name = '';
			$getpartner = $this->Crud->read_single('id', $partner_id, 'ka_offer_partner');
			if(!empty($getpartner)){
				foreach($getpartner as $partner){
					$ct_name = $this->Crud->country_data($partner->country_id, 'name');
					$partner_name = $partner->name.' - '.$ct_name;
				}
			}
			
			$list_com .= '
				<tr>
					<td>'.$partner_name.'</td>
					<td>'.$name.'</td>
					<td>'.$com.'%</td>
					<td>
						<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/offer/a_com/'.$id).'"><i class="mdi mdi-pencil"></i></a>
						<a class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Delete Record" href="'.base_url('admin/offer/d_com/'.$id).'"><i class="mdi mdi-close"></i></a>
					</td>
				</tr>
			';
		}
	}
		
	// list all offers
	$list_offer = '';
	if(!empty($alloffer)){
		foreach($alloffer as $offer) {
			$offer_id = $offer->id;
			$offer_com_id = $offer->com_id;
			$offer_user_id = $offer->user_id;
			$offer_save_id = $offer->saving_id;
			$offer_interest = $offer->interest;
			$offer_no = $offer->offer_no;
			$offer_product = $offer->product_link;
			$offer_status = $offer->status;
			
			if($offer_status == 'Approved') {
				$alert = 'success';
			} else if($offer_status == 'Pending') {
				$alert = 'info';
			} else if($offer_status == 'Withdrawn') {
				$alert = 'warning';
			} else if($offer_status == 'Declined') {
				$alert = 'danger';
			}
			
			// get commission details
			$offer_com_name = '';
			$offer_com = '';
			$partner = '';
			$getcom = $this->Crud->read_single('id', $offer_com_id, 'ka_offer_commission');
			if(!empty($getcom)) {
				foreach($getcom as $gcom) {
					$offer_com_name = $gcom->name;
					$offer_com = $gcom->com;
					
					// get partner name
					$getpart = $this->Crud->read_single('id', $gcom->partner_id, 'ka_offer_partner');
					if(!empty($getpart)) {
						foreach($getpart as $part) {
							$partner = $part->name;
							$partner = $partner.' - '.$this->Crud->country_data($part->country_id, 'name');	
						}
					}
				}
			}
			
			// get user information
			$p_saving_by = '';
			$p_curr = '';
			$p_user_id = '';
			$logo_path = 'assets/images/users/avatar300.png';
			$getuser = $this->Crud->read_single('id', $offer_user_id, 'ka_user');
			if(!empty($getuser)){
				foreach($getuser as $user){
					$p_user_id = $user->id;
					$p_saving_by = ucwords($user->othername).' '.ucwords($user->lastname);	
					
					//get logo
					$getimg = $this->Crud->read_single('id', $user->pics, 'ka_img');
					if(!empty($getimg)){
						foreach($getimg as $img){
							$logo_path = $img->pics_square;	
						}
					}
					
					// get currency
					$p_curr = $this->Crud->country_data($user->country, 'currency');
				}
			}
			
			// get savings
			$getsave = $this->Crud->read_single('id', $offer_save_id, 'ka_personal');
			if(!empty($getsave)){
				foreach($getsave as $save){
					$save_id = $save->id;
					$save_name = ucwords($save->name);
					$save_target = $p_curr.number_format((float)$save->target);
				}
			}
			
			$offer_interest = $p_curr.number_format((float)$offer_interest, 2);
			
			$list_offer .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.$offer_no.'</td>
					<td><img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30"></td>
					<td><a href="'.base_url('profile/v/'.$p_user_id).'">'.$p_saving_by.'</a></td>
					<td>'.ucwords($save_name).' <br/>'.$save_target.'</td>
					<td><b>'.$partner.'</b><br /><small class="text-primary">'.$offer_com_name.' ('.$offer_com.'% - '.$offer_interest.')</small></td>
					<td><b>'.strtoupper($offer_status).'</b></td>
					<td>
						<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/offer/m_offer/'.$offer_id).'"><i class="mdi mdi-pencil"></i></a>
						<a class="btn btn-xs btn-success" type="button" data-toggle="tooltip" title="View Product" href="'.$offer_product.'" target="_blank"><i class="mdi mdi-eye"></i></a>
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
                All offers.
                <?php if($param1 == ''){ ?>
                    <a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/offer/a_com/'); ?>"><i class="mdi mdi-plus-circle-outline"></i> Add Offer</a>&nbsp;
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/offer/a_partner/'); ?>"><i class="mdi mdi-plus-circle-outline"></i> Add Partner</a>
                <?php } else { ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/offer/'); ?>"><i class="mdi mdi-close-circle-outline"></i> Cancel</a>
                <?php } ?>
            </p>
        </div>
    </div>
    
    <?php if(!empty($err_msg)){echo $err_msg;} ?>
    
    <?php if($param1 == 'a_partner'){ // update offer ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/offer/a_partner', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <input type="hidden" name="partner_id" value="<?php if(!empty($e_id)){echo $e_id;} ?>" />
                    <?php
						$country_list = '';
						if(!empty($allcountry)){
							foreach($allcountry as $ac){
								$a_sel = '';
								if(!empty($e_country_id)){
									if($e_country_id == $ac->id){$a_sel = 'selected="selected"';}	
								}
								$country_list .= '<option value="'.$ac->id.'" '.$a_sel.'>'.$ac->name.'</option>';
							}
						}
					?>
                    <label for="role">Country</label>
                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="country_id" name="country_id">
                        <option></option>
                        <?php echo $country_list; ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <label for="role">Partner Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php if(!empty($e_name)){echo $e_name;} ?>"required />
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
    
    <?php if($param1 == 'd_partner'){ // delete partner ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/offer/d_partner', array('class'=>'form-horizontal')); ?>
            <div class="form-group col-xs-12">
                <input type="hidden" name="d_partner_id" value="<?php if(!empty($d_id)){echo $d_id;} ?>" />
                <h4>Are you sure you want to remove <?php echo $d_name; ?>?</h4>
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
    
    <?php if($param1 == 'a_com'){ // update offer commission ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/offer/a_com', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <input type="hidden" name="com_id" value="<?php if(!empty($e_id)){echo $e_id;} ?>" />
                    <?php
						$partner_list = '';
						if(!empty($allpartner)){
							foreach($allpartner as $p){
								$c_sel = '';
								if(!empty($e_partner_id)){
									if($e_partner_id == $p->id){$c_sel = 'selected="selected"';}	
								}
								// get country
								$c_name = $this->Crud->country_data($p->country_id, 'name');
								$partner_list .= '<option value="'.$p->id.'" '.$c_sel.'>'.$p->name.' - '.$c_name.'</option>';
							}
						}
					?>
                    <label for="role">Partner</label>
                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="partner_id" name="partner_id">
                        <option></option>
                        <?php echo $partner_list; ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <label for="role">Offer Name/Category</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php if(!empty($e_name)){echo $e_name;} ?>"required />
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group col-xs-12">
                    <label for="role">Commission % (e.g. 5)</label>
                    <input type="text" id="com" name="com" class="form-control" value="<?php if(!empty($e_com)){echo $e_com;} ?>"required />
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
    
    <?php if($param1 == 'd_com'){ // delete offer commissions ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/offer/d_com', array('class'=>'form-horizontal')); ?>
            <div class="form-group col-xs-12">
                <input type="hidden" name="d_com_id" value="<?php if(!empty($d_id)){echo $d_id;} ?>" />
                <h4>Are you sure you want to remove <?php echo $d_name; ?>?</h4>
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
    
    <?php if($param1 == 'm_offer'){ // update offer ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/offer/m_offer', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12 col-sm-6">
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <input type="hidden" name="offer_id" value="<?php if(!empty($e_id)){echo $e_id;} ?>" />
                        <input type="hidden" name="offer_user_id" value="<?php if(!empty($e_offer_user_id)){echo $e_offer_user_id;} ?>" />
                        <input type="hidden" name="offer_no" value="<?php if(!empty($e_offer_no)){echo $e_offer_no;} ?>" />
                        <label>Offer ID</label><br />
                        <?php if(!empty($e_offer_no)){echo $e_offer_no;} ?>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="role">Product Link</label><br />
                        <span class="text-danger"><a href="<?php if(!empty($e_product_link)){echo $e_product_link;} ?>" target="_blank"><?php if(!empty($e_product_link)){echo $e_product_link;} ?></span></a>
                    </div>
                </div>
                
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="role">Offer Link</label><br />
                        <span class="text-danger"><?php if(!empty($e_offer_link)){echo $e_offer_link;} ?></span>
                    </div>
                </div>
           	</div>
            
            <div class="col-xs-12 col-sm-6">
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="status">Money Back <small class="text-muted">(must based on Product price)</small></label>
                        <input type="text" name="offer_interest" name="offer_interest" class="form-control" value="<?php if(!empty($e_offer_interest)){echo $e_offer_interest;} ?>" />
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <?php
                            if(!empty($e_status)) {
                                if($e_status == 'Pending') {$s1='selected="selected"';} else {$s1='';}	
                                if($e_status == 'Approved') {$s2='selected="selected"';} else {$s2='';}	
                                if($e_status == 'Withdrawn') {$s3='selected="selected"';} else {$s3='';}	
                                if($e_status == 'Declined') {$s4='selected="selected"';} else {$s4='';}		
                            }
                        ?>
                        <label for="status">Status</label>
                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="status" name="status" <?php if($e_status!='Pending'){echo 'disabled';} ?>>
                            <option></option>
                            <option value="Pending" <?php echo $s1; ?>>Pending</option>
                            <option value="Approved" <?php echo $s2; ?>>Approved</option>
                            <option value="Withdrawn" <?php echo $s3; ?>>Withdrawn</option>
                            <option value="Declined" <?php echo $s4; ?>>Declined</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="reason">Reason <small class="text-muted">(if Declined or Money Back changes)</small></label>
                        <textarea name="reason" id="reason" class="form-control" rows="5"></textarea>
                    </div>
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
    
    <div class="m-t-30">
        <ul class="nav nav-tabs tabs-bordered">
            <li class="<?php if($param1=='' || $param1=='a_partner' || $param1=='d_partner'){echo 'active';} ?>"> <a href="#p-tab" data-toggle="tab" aria-expanded="true"> PARTNERS </a> </li>
            <li class="<?php if($param1=='a_com' || $param1=='d_com'){echo 'active';} ?>"> <a href="#c-tab" data-toggle="tab" aria-expanded="false"> COMMISSIONS </a> </li>
            <li class="<?php if($param1=='m_offer'){echo 'active';} ?>"> <a href="#o-tab" data-toggle="tab" aria-expanded="false"> OFFERS </a> </li>
        </ul>
        <div class="tab-content">
        	<div class="tab-pane <?php if($param1=='' || $param1=='a_partner' || $param1=='d_partner'){echo 'active';} ?>" id="p-tab">
            	<div class="row">
                    <div class="col-xs-12">
                        <div class="m-b-20 table-responsive">
                            <table id="datatable-buttons" class="table table-hover table-striped small">
                                <thead>
                                    <tr>
                                        <th><b>COUNTRY</b></th>
                                        <th><b>PARTNER</b></th>
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
            </div>
            
            <div class="tab-pane <?php if($param1=='a_com' || $param1=='d_com'){echo 'active';} ?>" id="c-tab">
            	<div class="row">
                    <div class="col-xs-12">
                        <div class="m-b-20 table-responsive">
                            <table id="datatable-buttons" class="table table-hover table-striped small">
                                <thead>
                                    <tr>
                                        <th><b>PARTNER</b></th>
                                        <th><b>OFFER</b></th>
                                        <th><b>COMMISSION</b></th>
                                        <th width="50px"><b></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $list_com; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane <?php if($param1=='m_offer'){echo 'active';} ?>" id="o-tab">
            	<div class="row">
                    <div class="col-xs-12">
                        <div class="m-b-20 table-responsive">
                            <table id="datatable-buttons" class="table table-hover table-striped small">
                                <thead>
                                    <tr>
                                        <th><b>OFFER ID</b></th>
                                        <th><b></b></th>
                                        <th><b>USER</b></th>
                                        <th><b>SAVINGS</b></th>
                                        <th><b>OFFER</b></th>
                                        <th><b>STATUS</b></th>
                                        <th width="50px"><b></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $list_offer; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
      	</div>
  	</div>
    
</div>
