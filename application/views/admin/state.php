<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">States</h4>
            <p class="text-muted m-b-30">
                All registered states.
                <?php if($param1 == ''){ ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/state/a/'); ?>"><i class="mdi mdi-plus-circle-outline"></i> Add New</a>
                <?php } else { ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/state/'); ?>"><i class="mdi mdi-close-circle-outline"></i> Cancel</a>
                <?php } ?>
            </p>
        </div>
    </div>
    
    <?php if(!empty($err_msg)){echo $err_msg;} ?>
    
    <?php if($param1 == 'a'){ // update ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/state/a', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-6">
                <div class="form-group col-xs-12">
                    <input type="hidden" name="state_id" value="<?php if(!empty($e_id)){echo $e_id;} ?>" />
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
            <div class="col-xs-6">
                <div class="form-group col-xs-12">
                    <label for="role">State Name</label>
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
    
    <?php if($param1 == 'd'){ // delete ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/state/d', array('class'=>'form-horizontal')); ?>
            <div class="form-group col-xs-12">
                <input type="hidden" name="d_country_id" value="<?php if(!empty($d_id)){echo $d_id;} ?>" />
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
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="dtable" class="table table-hover table-striped display responsive nowrap small" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><b>COUNTRY</b></th>
                            <th><b>STATE</b></th>
                            <th width="50"><b></b></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
