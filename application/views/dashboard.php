<?php
	$active_target = 0;
	$active_save = 0;
	$active_percent = 0;
	$lifetime_target = 0;
	$lifetime_save = 0;
	$lifetime_percent = 0;
	$active_list = '';
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
			
			// calculate savings percentage
			$percent = ($saves / (float)$target) * 100;
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
						<a class="pull-right text-success" href="'.base_url('savings/personal/v/'.$id).'" title="View Savings"><i class="ti-eye"></i></a>
						<input data-plugin="knob" data-width="150" data-height="150" data-min="0"
							   data-fgColor="'.$knob_color.'" data-displayPrevious=false data-angleOffset=0
							   data-angleArc=360 value="'.number_format($percent).'" readonly="readonly"/>
						<div class="progress progress-sm m-0">
							<div class="progress-bar progress-bar-'.$alert.' progress-bar-striped active" role="progressbar" aria-valuenow="'.number_format($percent).'" aria-valuemin="0" aria-valuemax="100" style="width: '.number_format($percent).'%;">
								<span class="sr-only">'.$percent.'% Complete</span>
							</div>
						</div>
						<div class="m-t-10 small">
							<b>'.ucwords($name).'</b> - '.$my_curr.number_format($saves).' of '.$my_curr.number_format((float)$target).'
						</div>
					</div>
				</div>
			';
			
			if($active == 1){
				$active_list .= $list;
				$active_save = $active_save + $saves;
				$active_target = $active_target + (float)$target;
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
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Dashboard</h4>
        </div>
    </div>
    
    <div class="row">
        <?php echo $active_list ? $active_list : '<h3 class="text-center text-muted">No Active Savings Yet!<br /><br /><a href="'.base_url('savings/personal/add').'" class="btn btn-primary"><i class="mdi mdi-wallet"></i> Start A Plan</a> <a href="'.base_url('savings/personal').'" class="btn btn-success"><i class="mdi mdi-wallet"></i> All Plans</a></h3>'; ?>
    </div>
    <!--end row -->
    
    <!--<div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="m-t-0">Active Contributions (<?php echo date('Y'); ?>)</h4>
                <div id="morris-line-example" style="height: 300px;"></div>
            </div>
        </div>
 	</div>-->
    <?php
		//echo $msg.'<hr/>';
//		echo $result->status.'<br />';
//		$bkitem = $banks->data;
//		echo $banks->message.'<hr/>';
//		foreach($bkitem as $key => $value){
//			echo $key.'-'.$value.'<br />';	
//		}
//		echo '<hr />'.$getacc;
	?>
</div>
