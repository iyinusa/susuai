<?php
	$bot = 0;
	$direct = 0;
	$bot_per = 0;
	$direct_per = 0;
	$list = '';
	if(!empty($alluser)){
		foreach($alluser as $user){
			if($user->fbbot_psid != ''){
				$bot += 1;
			} else {
				$direct += 1;
			}
		}
		
		$bot_per = number_format($bot / ($bot + $direct) * 100);
		$direct_per = number_format($direct / ($bot + $direct) * 100);
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Registered Users</h4>
            <p class="text-muted m-b-30">
                All registered users. <span class="label label-primary"><b>Direct: </b><?php echo $direct.' ('.$direct_per.'%)'; ?></span> <span class="label label-success"><b>Bot: </b><?php echo $bot.' ('.$bot_per.'%)'; ?></span>
            </p>
        </div>
    </div>
    
    <?php if($edit == TRUE) { ?>
    <div class="row">
        <div class="col-xs-12">
        	<?php echo form_open('admin/user/'.$e_id, array('class'=>'form-horizontal')); ?>
            <div class="col-xs-12">
         		<div class="form-group">
                	<b><?php echo $e_name; ?></b>
                </div>
          	</div>
            <div class="col-sm-4">
                <div class="col-xs-12">
                    <div class="form-group">
                        <?php
                            if($e_role == 'User'){$r1 = 'selected="selected"';} else {$r1 = '';}
                            if($e_role == 'Admin'){$r2 = 'selected="selected"';} else {$r2 = '';}
                        ?>
                        <label for="role">Role</label><br />
                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="role" name="role">
                            <option></option>
                            <option value="User" <?php echo $r1; ?>>User</option>
                            <option value="Admin" <?php echo $r2; ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <?php
                            if($e_activate == 0){$a1 = 'selected="selected"';} else {$a1 = '';}
                            if($e_activate == 1){$a2 = 'selected="selected"';} else {$a2 = '';}
                        ?>
                        <label for="role">Activation</label><br />
                        <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="activate" name="activate">
                            <option></option>
                            <option value="0" <?php echo $a1; ?>>No</option>
                            <option value="1" <?php echo $a2; ?>>Yes</option>
                        </select>
                    </div>
                </div>
           	
                <div class="col-xs-12">
                    <div class="form-group">
                        <button class="btn btn-primary waves-effect waves-light w-md" type="submit"><i class="mdi mdi-content-save-all"></i> Save Record</button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
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
                            <th><b>DATE</b></th>
                            <th><b></b></th>
                            <th><b>NAME</b></th>
                            <th><b>EMAIL</th>
                            <th><b>PHONE</b></th>
                            <th><b>SEX</b></th>
                            <th><b>ROLE</b></th>
                            <th><b></b></th>
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
