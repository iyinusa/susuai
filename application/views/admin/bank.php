<?php
	$list = '';
	if(!empty($allbank)){
		foreach($allbank as $bk){
			$id = $bk->id;
			$name = $bk->name;
			$code = $bk->code;
			
			$country_name = '';
			$getcountry = $this->Crud->read_single('id', $bk->country_id, 'ka_country');
			if(!empty($getcountry)) {
				foreach($getcountry as $country){
					$country_name = $country->name;
				}
			}
			
			$list .= '
				<tr>
					<td>'.$code.'</td>
					<td>'.$country_name.'</td>
					<td>'.$name.'</td>
				</tr>
			';
		}
	}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Banks</h4>
            <p class="text-muted m-b-30">
                All banks and respective code.
            </p>
        </div>
    </div>
    
    <?php if(!empty($err_msg)){echo $err_msg;} ?>
    
    <div class="row">
        <div class="col-xs-12">
        	<div class="m-b-20 table-responsive">
                <table id="datatable-buttons" class="table table-hover table-striped small">
                    <thead>
                        <tr>
                            <th width="50"><b>CODE</b></th>
                            <th><b>COUNTRY</th>
                            <th><b>NAME</th>
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
