<?php
	$lifetime = 0;
	$current = 0;
	$withdraw = 0;
	$refund = 0;
	$list = '';
	if(!empty($allvault)){
		foreach($allvault as $vault){
			$id = $vault->id;
			$user_id = $vault->user_id;
			$item_id = $vault->item_id;
			$purpose = $vault->purpose;
			$type = $vault->type;
			$amt = $vault->amt;
			$reg_date = $vault->reg_date;
			
			if($type == 'save') {
				$lifetime = $lifetime + (float)$amt;
				$alert = 'success';
			} else if($type == 'withdraw') {
				$withdraw = $withdraw + (float)$amt;
				$alert = 'danger';
			} else if($type == 'refund') {
				$refund = $refund + (float)$amt;
				$alert = 'warning';
			}
			
			// get purpose details
			$purpose_name = '';
			if($purpose == 'personal'){
				$getpur = $this->Crud->read_single('id', $item_id, 'ka_personal');
				if(!empty($getpur)){
					foreach($getpur as $pur){
						$purpose_name = $pur->name.' Contribution';	
					}
				}
			}
			
			if($purpose_name == ''){$purpose_name = $purpose;}
			
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
			
			// get listing
			$list .= '
				<tr class="alert alert-'.$alert.'">
					<td>'.date('d M, Y h:i:s A', strtotime($reg_date)).'</td>
					<td>'.$p_saving_by.'</td>
					<td>'.ucwords($purpose_name).'</td>
					<td>'.ucwords($type).'</td>
					<td class="text-right">'.number_format($amt,2).'</td>
				</tr>
			';
		}
		
		$current = $lifetime - $withdraw;
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Vault/Wallet</h4>
            <p class="text-muted m-b-30">
                Keep track of all accounts here.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="widget-inline">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-info mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($lifetime,2); ?></b></h4>
                            <p class="text-muted">Lifetime</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-success mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($current,2); ?></b></h4>
                            <p class="text-muted">Current</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h4><i class="text-danger mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($withdraw,2); ?></b></h4>
                            <p class="text-muted">Withdraw</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center b-0">
                            <h4><i class="text-warning mdi mdi-wallet"></i><br/><b data-plugin="counterup"><?php echo my_curr.number_format($refund,2); ?></b></h4>
                            <p class="text-muted">Refund</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div><hr />
    <!--end row -->
    
    <div class="row">
    	<div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="datatable-buttons" class="table table-hover table-striped small">
                    <thead>
                        <tr>
                            <th><b>DATE</b></th>
                            <th><b>BY</b></th>
                            <th><b>PURPOSE</b></th>
                            <th><b>TYPE</b></th>
                            <th class="text-right"><b>AMOUNT (<?php echo my_curr ?>)</b></th>
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
