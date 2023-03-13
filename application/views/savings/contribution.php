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
            <h4 class="header-title m-t-0">Contributions</h4>
            <p class="text-muted m-b-30">
                See details of every contributions you made.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="datatable-buttons" class="table table-hover table-striped small">
                    <thead>
                        <tr>
                            <th><b>DATE</b></th>
                            <th><b>SAVING</b></th>
                            <th class="text-right"><b>AMOUNT (<?php echo $my_curr ?>)</b></th>
                            <th><b>TYPE</b></th>
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
</div>
