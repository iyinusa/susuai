<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Countries</h4>
            <p class="text-muted m-b-30">
                All registered country.
                <?php if($param1 == ''){ ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/country/a/'); ?>"><i class="mdi mdi-plus-circle-outline"></i> Add New</a>
                <?php } else { ?>
                	<a class="btn btn-default btn-sm pull-right" href="<?php echo base_url('admin/country/'); ?>"><i class="mdi mdi-close-circle-outline"></i> Cancel</a>
                <?php } ?>
            </p>
        </div>
    </div>
    
    <?php if(!empty($err_msg)){echo $err_msg;} ?>
    
    <?php if($param1 == 'a'){ // update ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/country/a', array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12">
                <div class="form-group col-xs-12">
                    <input type="hidden" name="country_id" value="<?php if(!empty($e_id)){echo $e_id;} ?>" />
                    <label for="role">Country Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php if(!empty($e_name)){echo $e_name;} ?>"required />
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group col-xs-12">
                    <label for="role">Country Code</label>
                    <input type="text" id="code" name="code" class="form-control" value="<?php if(!empty($e_code)){echo $e_code;} ?>" />
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group col-xs-12">
                    <label for="role">Currency</label>
                    <input type="text" id="currency" name="currency" class="form-control" value="<?php if(!empty($e_currency)){echo $e_currency;} ?>" />
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
        	<?php echo form_open('admin/country/d', array('class'=>'form-horizontal')); ?>
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
                            <th width="50"><b>CODE</b></th>
                            <th><b>NAME</b></th>
                            <th width="50"><b>CURRENCY</b></th>
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
