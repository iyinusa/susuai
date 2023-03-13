<?php
	$list = '';
	if(!empty($allacc)){
		foreach($allacc as $acc){
			$id = $acc->id;
			$bank_id = $acc->bank_id;
			$acc_name = $acc->acc_name;
			$acc_no = $acc->acc_no;
			$acc_desc = $acc->acc_desc;
			
			// get bank name
			$bank_name = '';
			$getbank = $this->Crud->read_single('id', $bank_id, 'ka_bank');
			if(!empty($getbank)){
				foreach($getbank as $bank){
					$bank_name = $bank->name;
				}
			}
			
			// get linked savings
			$count = 0;
			$link_saving = '';
			$getlink = $this->Crud->read_single('acc_id', $id, 'ka_account_link');
			if(!empty($getlink)){
				foreach($getlink as $link){
					$getpers = $this->Crud->read_single('id', $link->saving_id, 'ka_personal');
					if(!empty($getpers)){
						foreach($getpers as $pers){
							$link_saving .= $pers->name.', ';
						}
					}
					$count += 1;
				}
			}
			
			if($acc_desc != ''){$acc_desc = ' - <b>'.$acc_desc.'</b>';}
			
			if($link_saving != ''){$list_bg = 'style="background-color:#f9e5e5;"';} else {$list_bg = '';}
			
			$list .= '
				<div class="card-box col-lg-4 col-sm-6 col-xs-12" '.$list_bg.'>
					<div class="text-center">
						<span class="text-muted">'.$bank_name.'</span>
						<h4><b data-plugin="counterup">'.$acc_name.'</b><br /><span class="small">'.$acc_no.$acc_desc.'</span></h4>
						<p class="text-primary">Linked to: <b>'.ucwords($link_saving).'</b></p>
					</div>
				</div>
			';
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Accounts</h4>
            <p class="text-muted m-b-30">
                Manage all disbursement accounts and linked savings here.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="widget-inline">
                <div class="row">
                    <?php echo $list; ?>
                </div>
            </div>
        </div>
    </div>
    <!--end row -->
</div>
