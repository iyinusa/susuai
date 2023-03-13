<?php
	$list = '';
	if(!empty($allpersonal)){
		foreach($allpersonal as $per){
			$id = $per->id;
			$user_id = $per->user_id;
			$name = $per->name;
			$target = $per->target;
			$type = $per->type;
			$duration = $per->duration;
			$cycle = $per->cycle;
			$expired = $per->expired;
			$reg_date = $per->reg_date;
			
			// get user detail
			$p_saving_by = '';
			$logo_path = 'assets/images/users/avatar300.png';
			$getuser = $this->Crud->read_single('id', $user_id, 'ka_user');
			if(!empty($getuser)){
				foreach($getuser as $user){
					$p_saving_by = ucwords($user->othername).' '.ucwords($user->lastname);	
					
					//get logo
					$getimg = $this->Crud->read_single('id', $user->pics, 'ka_img');
					if(!empty($getimg)){
						foreach($getimg as $img){
							$logo_path = $img->pics_square;	
						}
					}
				}
			}
			
			if($expired == 1){$alert = 'danger';} else {$alert = '';}
			
			$list .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.$id.'</td>
					<td>'.date('d M, Y', strtotime($reg_date)).'</td>
					<td><img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30"></td>
					<td><a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a></td>
					<td>'.ucwords($name).'</td>
					<td>'.number_format((float)$target,2).'</td>
					<td>'.$type.'</td>
					<td>'.$cycle.' of '.$duration.'</td>
				</tr>
			';
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Personal Savings</h4>
            <p class="text-muted m-b-30">
                All personal savings by users.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="dtable" class="table table-hover table-striped display responsive nowrap small" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="25"><b>ID</b></th>
                            <th><b>DATE</b></th>
                            <th><b></b></th>
                            <th><b>BY</b></th>
                            <th><b>SAVING</th>
                            <th><b>TARGET (<?php echo my_curr; ?>)</b></th>
                            <th><b>TYPE</b></th>
                            <th><b>CYCLE</b></th>
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
