<?php
	$list = '';
	if(!empty($allcont)){
		foreach($allcont as $cont){
			$id = $cont->id;
			$saving_id = $cont->saving_id;
			$amt = $cont->amt;
			$type = $cont->type;
			$reg_date = $cont->reg_date;
			
			// get saving details
			$p_saving_by = '';
			$save_id = '';
			$save_name = '';
			$user_id = '';
			$logo_path = 'assets/images/users/avatar300.png';
			$getsave = $this->Crud->read_single('id', $saving_id, 'ka_personal');
			if(!empty($getsave)){
				foreach($getsave as $save){
					$save_id = $save->id;
					$save_name = ucwords($save->name);
					
					// get user detail
					$getuser = $this->Crud->read_single('id', $save->user_id, 'ka_user');
					if(!empty($getuser)){
						foreach($getuser as $user){
							$user_id = $user->id;
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
				}
			}
			
			$list .= '
				<tr>
					<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
					<td><img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30"></td>
					<td><a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a></td>
					<td>'.ucwords($save_name).'</td>
					<td>'.number_format((float)$amt,2).'</td>
					<td>'.$type.'</td>
				</tr>
			';
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Contributions</h4>
            <p class="text-muted m-b-30">
                All contributions activities by users.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="dtable" class="table table-hover table-striped display responsive nowrap small" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50px"><b>ID</b></th>
                            <th><b>DATE</b></th>
                            <th><b></b></th>
                            <th><b>BY</b></th>
                            <th><b>SAVING</th>
                            <th><b>AMOUNT (<?php echo my_curr; ?>)</b></th>
                            <th><b>METHOD</b></th>
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
