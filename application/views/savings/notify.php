<?php
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
					
					$list .= '
						<tr>
							<td>'.date('d M, Y h:i:s A', strtotime($contr->reg_date)).'</td>
							<td><a href="'.base_url('savings/personal/v/'.$id).'">'.ucwords($name).'</a></td>
							<td class="text-right">'.number_format((float)$contr->amt,2).'</td>
							<td>'.$contr->type.'</td>
						</tr>
					';	
				}
			}
			
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Payment Notification</h4>
            <p class="text-muted m-b-30">
                See details of your current payment.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="text-center m-b-20">
                <img src="<?php echo base_url(); ?>assets/images/<?php echo $status_icon; ?>" height="80" class="m-t-10">
                <h3 class="text-muted m-t-30 line-h-24"> <?php echo $msg; ?> </h3>
                <?php echo $err_msg; ?><br /><br />
                <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-primary"><i class="mdi mdi-home"></i> Dashboard</a>
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
