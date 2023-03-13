<?php
	$active_target = 0;
	$active_save = 0;
	$active_percent = 0;
	$active_alert = '';
	$lifetime_target = 0;
	$lifetime_save = 0;
	$lifetime_percent = 0;
	$lifetime_alert = '';
	$active_list = '';
	$inactive_list = '';
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
			$cycle = $pers->cycle;
			$active = $pers->active;
			$expired = $pers->expired;
			$reg_date = $pers->reg_date;
			
			// get contributions
			$saves = 0;
			$getcontr = $this->Crud->read_single('saving_id', $pers->id, 'ka_contribute');
			if(!empty($getcontr)){
				foreach($getcontr as $contr){
					$saves = $saves + (float)$contr->amt;	
				}
			}
			
			// show delete button to only inactive savings
			if($active == 0 && $expired == 0){
				$per_del = '
					<a class="text-danger" href="javascript:;" data-toggle="modal" data-target="#plan_delete'.$id.'" title="Delete Plan"><i class="mdi mdi-close-circle-outline"></i></a>
					<div id="plan_delete'.$id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel'.$id.'" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title" id="myModalLabel'.$id.'">Delete Plan</h4>
								</div>
								<div class="modal-body">
									<h2>Delete '.ucwords($name).'</h2>
									<h4 class="text-danger">Are you sure?</h4>
									<div id="del_resp'.$id.'"></div>
									
									<script type="text/javascript">
										function delplan'.$id.'(){
											var hr = new XMLHttpRequest();
											var del_id = '.$id.';
											var del_name = '.$name.';
											var c_vars = "del_id="+del_id+"&del_name="+del_name;
											hr.open("POST", "'.base_url().'savings/personal/delete/'.$id.'", true);
											hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
											hr.onreadystatechange = function() {
												if(hr.readyState == 4 && hr.status == 200) {
													var return_data = hr.responseText;
													document.getElementById("del_resp'.$id.'").innerHTML = return_data;
											   }
											}
											hr.send(c_vars);
											document.getElementById("del_resp'.$id.'").innerHTML = "<i class=\"icon-spin4 animate-spin loader\"></i> Deleting...";
										}
									</script>
								</div>
								<div class="modal-footer">
									<a href="javascript:;" class="btn btn-default" data-dismiss="modal"><i class="ti-close"></i> No</a>
									<a href="javascript:;" class="btn btn-danger" onclick="delplan'.$id.'()"><i class="ti-trash"></i> Yes - Delete</a>
								</div>
							</div>
						</div>
					</div>
				';
			} else {
				$per_del = '';
			}
			
			// calculate savings percentage
			$percent = ($saves / (float)$target) * 100;
			$alert = 'primary';
			$knob_color = '#458BC7';
			if(number_format($percent) >= 0 && number_format($percent) < 20){
				$knob_color = '#cccccc'; $alert = 'danger';	
			} else if(number_format($percent) < 40 && number_format($percent) >= 20){
				$knob_color = '#f0ad4e'; $alert = 'warning';	
			} else if(number_format($percent) < 60 && number_format($percent) >= 40){
				$knob_color = '#5bc0de'; $alert = 'info';	
			} else if(number_format($percent) < 80 && number_format($percent) >= 60){
				$knob_color = '#337ab7'; $alert = 'primary';	
			} else if(number_format($percent) > 80) {
				$knob_color = '#5cb85c'; $alert = 'success';
			}
			
			$list = '
				<div class="col-md-3 col-sm-6 text-center">
					<div class="p-20 m-b-20">
						<span class="pull-right">
							<a class="text-success" href="'.base_url('savings/personal/v/'.$id).'" title="View Savings"><i class="mdi mdi-eye"></i></a>&nbsp;'.$per_del.'
						</span>
						<input data-plugin="knob" data-width="150" data-height="150" data-min="0"
							   data-fgColor="'.$knob_color.'" data-displayPrevious=false data-angleOffset=0
							   data-angleArc=360 value="'.number_format($percent).'" readonly="readonly"/>
						<div class="progress progress-sm m-0">
							<div class="progress-bar progress-bar-'.$alert.' progress-bar-striped active" role="progressbar" aria-valuenow="'.number_format($percent).'" aria-valuemin="0" aria-valuemax="100" style="width: '.number_format($percent).'%;">
								<span class="sr-only">'.$percent.'% Complete</span>
							</div>
						</div>
						<div class="m-t-10 small">
							<b>'.ucwords($name).'</b><br />'.$my_curr.number_format($saves).' of '.$my_curr.number_format((float)$target).'
						</div>
					</div>
				</div>
			';
			
			if($active == 1){
				$active_list .= $list;
				$active_save = $active_save + $saves;
				$active_target = $active_target + (float)$target;
			} else {
				$inactive_list .= $list;
			}
			
			$lifetime_save = $lifetime_save + $saves;
			$lifetime_target = $lifetime_target + (float)$target;
		}
		
		if($active_target > 0){$active_percent  = ($active_save / $active_target) * 100;}
		if($lifetime_target > 0){$lifetime_percent = ($lifetime_save / $lifetime_target) * 100;}
		
		if($active_percent >= 0 && $active_percent < 20){ $active_alert = 'danger';	
		} else if($active_percent < 40 && $active_percent >= 20){ $active_alert = 'warning';	
		} else if($active_percent < 60 && $active_percent >= 40){ $active_alert = 'info';	
		} else if($active_percent < 80 && $active_percent >= 60){ $active_alert = 'primary';	
		} else if($active_percent > 80) { $active_alert = 'success'; }
		
		if($lifetime_percent >= 0 && $lifetime_percent < 20){ $lifetime_alert = 'danger';	
		} else if($lifetime_percent < 40 && $lifetime_percent >= 20){ $lifetime_alert = 'warning';	
		} else if($lifetime_percent < 60 && $lifetime_percent >= 40){ $lifetime_alert = 'info';	
		} else if($lifetime_percent < 80 && $lifetime_percent >= 60){ $lifetime_alert = 'primary';	
		} else if($lifetime_percent > 80) { $lifetime_alert = 'success'; }
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Personal Savings <a href="<?php echo base_url('savings/personal/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="mdi mdi-plus-circle-outline"></i> Add Saving Plan</a></h4>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-xs-12 col-sm-6">
        	<div class="card-box">
                <a href="#" class="btn btn-sm btn-default pull-right" title="View Details"><i class="ti-eye"></i></a>
                <h4 class="text-muted m-t-0 text-uppercase">Active</h4><br/>
                <h3 class="m-b-20 row">
                	<span class="pull-left text-<?php echo $active_alert; ?>"><?php echo $my_curr.number_format($active_save); ?></span>
                    <span class="pull-right text-success"><?php echo $my_curr.number_format($active_target); ?></span>
                </h3>
                <div class="progress progress-sm m-0">
                    <div class="progress-bar progress-bar-<?php echo $active_alert; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo number_format($active_percent); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo number_format($active_percent); ?>%;">
                        <span class="sr-only"><?php echo number_format($active_percent); ?>% Complete</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-6">
        	<div class="card-box">
                <a href="#" class="btn btn-sm btn-default pull-right" title="View Details"><i class="ti-eye"></i></a>
                <h4 class="text-muted m-t-0 text-uppercase">Lifetime</h4><br/>
                <h3 class="m-b-20 row">
                	<span class="pull-left text-<?php echo $lifetime_alert; ?>"><?php echo $my_curr.number_format($lifetime_save); ?></span>
                    <span class="pull-right text-success"><?php echo $my_curr.number_format($lifetime_target); ?></span>
                </h3>
                <div class="progress progress-sm m-0">
                    <div class="progress-bar progress-bar-<?php echo $lifetime_alert; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo number_format($lifetime_percent); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo number_format($lifetime_percent); ?>%;">
                        <span class="sr-only"><?php echo number_format($lifetime_percent); ?>% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($active_target > 0){ ?>
    <div class="m-b-20 p-t-50">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0 text-success">Active Savings</h4>
            <p class="text-muted m-b-30">
                All currently active savings.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<?php echo $active_list; ?>
    </div>
    
    <div class="m-b-20 p-t-50">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0 text-danger">Inactive Savings</h4>
            <p class="text-muted m-b-30">
                Savings are not longer active due to partial/completed cycle.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<?php echo $inactive_list; ?>
    </div>
    
	<?php } else { ?>
    <div class="m-b-20 p-t-50">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0 text-danger">Inactive Savings</h4>
            <p class="text-muted m-b-30">
                Savings are not longer active due to partial/completed cycle.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<?php echo $inactive_list; ?>
    </div>
    
    <div class="m-b-20 p-t-50">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0 text-success">Active Savings</h4>
            <p class="text-muted m-b-30">
                All currently active savings.
            </p>
        </div>
    </div>
    
    <div class="row">
    	<?php echo $active_list; ?>
    </div>
    <? } ?>
    
</div>