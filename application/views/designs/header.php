<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
	if($page_active == 'dashboard'){$dash_act='active';}else{$dash_act='';}
	if($page_active == 'saving'){$saving_act='active';}else{$saving_act='';}
	if($page_active == 'personal'){$personal_act='active';}else{$personal_act='';}
	if($page_active == 'contribution'){$contribution_act='active';}else{$contribution_act='';}
	if($page_active == 'account'){$account_act='active';}else{$account_act='';}
	if($page_active == 'offer'){$offer_act='active';}else{$offer_act='';}
	if($page_active == 'vault'){$vault_act='active';}else{$vault_act='';}
	if($page_active == 'ad_country'){$ad_country_act='active';}else{$ad_country_act='';}
	if($page_active == 'ad_state'){$ad_state_act='active';}else{$ad_state_act='';}
	if($page_active == 'ad_user'){$ad_user_act='active';}else{$ad_user_act='';}
	if($page_active == 'ad_personal'){$ad_personal_act='active';}else{$ad_personal_act='';}
	if($page_active == 'ad_contribute'){$ad_contribute_act='active';}else{$ad_contribute_act='';}
	if($page_active == 'ad_vault'){$ad_vault_act='active';}else{$ad_vault_act='';}
	if($page_active == 'ad_bank'){$ad_bank_act='active';}else{$ad_bank_act='';}
	if($page_active == 'ad_transaction'){$ad_transaction_act='active';}else{$ad_transaction_act='';}
	if($page_active == 'ad_offer'){$ad_offer_act='active';}else{$ad_offer_act='';}
	
	// notification logic
	$list_hn = '';
	$list_hn_count = 0;
	$list_hn5_count = 0;
	$hn_alert = 'primary';
	$get_hnotify = $this->Crud->read_single('user_id', $this->session->userdata('kas_id'), 'ka_notify');
	if(!empty($get_hnotify)){
		foreach($get_hnotify as $hnotify){
			$hn_id = $hnotify->id;
			$hn_hash = $hnotify->nhash;
			$hn_item_id = $hnotify->item_id;
			$hn_item = $hnotify->item;
			$hn_new = $hnotify->new;
			$hn_title = $hnotify->title;
			$hn_details = $hnotify->details;
			$hn_type = $hnotify->type;
			$hn_reg_date = $hnotify->reg_date;
			
			$hn_reg_date = timespan(strtotime($hn_reg_date), time());
			$hn_reg_date = explode(',', $hn_reg_date);
			
			// identify notification
			if($hn_item == 'personal'){
				$hn_item_icon = 'mdi mdi-cash bg-success';
			} else if($hn_item == 'vault'){
				$hn_item_icon = 'mdi mdi-wallet bg-warning';
			} else {
				$hn_item_icon = 'mdi mdi-information bg-primary';
			}
			
			if($hn_new == 1){
				$list_hn_count += 1;
				$hn_each_alert = 'style="background-color:#D57171; color:#fff; display:block;"';
				$hn_each_alert_text = 'style="color:#eee;"';
			} else {
				$hn_each_alert = '';
				$hn_each_alert_text = '';
			}
			
			if($list_hn5_count <= 5){
				$list_hn .= '
					<a href="'.base_url('notifications/v/'.$hn_hash).'" class="list-group-item" '.$hn_each_alert.'>
						<div class="media">
							<div class="media-left p-r-10"> <em class="'.$hn_item_icon.'"></em> </div>
							<div class="media-body">
								<h5 class="media-heading">'.$hn_title.'</h5>
								<p class="m-0"> <small '.$hn_each_alert_text.'>'.substr($hn_details,0,30).'...<br/><span class="small">'.$hn_reg_date[0].' ago</span></small> </p>
							</div>
						</div>
					</a> 
				';
			}
			
			$list_hn5_count += 1;
		}
		if($list_hn_count > 0){$hn_alert = 'danger';}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="<?php echo app_meta_desc; ?>" name="description" />
<meta content="<?php echo app_name; ?>" name="author" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.png">

<?php if($page_active != 'dashboard'){ ?>
<!-- DataTables -->
<link href="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
        
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/switchery/switchery.min.css">
<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/plugins/clockpicker/css/bootstrap-clockpicker.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" rel="stylesheet" />
<?php } ?>

<?php if($page_active == 'dashboard'){ ?>
<!--Morris Chart CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/morris/morris.css">
<?php } ?>

<!-- Bootstrap core CSS -->
<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="<?php echo base_url(); ?>assets/css/metisMenu.min.css" rel="stylesheet">
<!-- Icons CSS -->
<link href="<?php echo base_url(); ?>assets/css/icons.css" rel="stylesheet">
<!-- Custom styles for this template -->
<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
</head>

<body>

<div id="page-wrapper">

<!-- Top Bar Start -->
<div class="topbar"> 
    
    <!-- LOGO -->
    <div class="topbar-left" style="width:220px;">
        <button type="button" class="button-menu-mobile visible-xs visible-sm pull-left"> <i class="mdi mdi-menu"></i> </button>
        
        <div class="">
        	<a href="<?php echo base_url(); ?>" class="logo"> <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="<?php echo app_name; ?>" class="logo-lg" /> <img src="<?php echo base_url(); ?>assets/images/logo_sm.png" alt="logo" class="logo-sm hidden" /> </a> 
        </div>
    </div>
    
    <!-- Top navbar -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class=""> 
                <!-- Top nav Right menu -->
                <ul class="nav navbar-nav navbar-right top-navbar-items-right pull-right">
                    <li class="dropdown top-menu-item-xs"> <a href="#" data-target="#" class="dropdown-toggle menu-right-item" data-toggle="dropdown" aria-expanded="true"> <i class="mdi mdi-bell"></i> <span class="label label-<?php echo $hn_alert; ?>"><?php echo $list_hn_count; ?></span> </a>
                        <ul class="dropdown-menu p-0 dropdown-menu-lg">
                            <li class="list-group notification-list" style="height: 267px;">
                                <div class="slimscroll"> 
                                   <?php if($list_hn == ''){ ?>
                                   <div style="text-align:center; padding:15px; font-size:large;" class="text-muted"> No notifications yet!</div>
                                   <?php } else {echo $list_hn;} ?>
                              	</div>
                            </li>
                            <div class="col-sm-12 text-muted" style="padding-bottom:5px;">
                                <a href="<?php echo base_url('notifications/clear'); ?>" class="pull-left"> 
                                	<small class="font-600">Read All</small> 
                            	</a>
                                <a href="<?php echo base_url('notifications/'); ?>" class="pull-right"> 
                                	<small class="font-600">See All</small> 
                            	</a> 
                            </div>
                        </ul>
                    </li>
                    <li class="dropdown top-menu-item-xs"> <a href="" class="dropdown-toggle menu-right-item profile" data-toggle="dropdown" aria-expanded="true"><img src="<?php echo base_url($this->session->userdata('kas_user_pics')); ?>" alt="user" class="img-circle"> </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('profile'); ?>"><i class="ti-user m-r-10"></i> Profile</a></li>
                            <li><a href="<?php echo base_url('vaults'); ?>"><i class="ti-wallet m-r-10"></i> Vault</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo base_url('logout'); ?>"><i class="ti-power-off m-r-10"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end container --> 
    </div>
    <!-- end navbar --> 
</div>
<!-- Top Bar End --> 

<!-- Page content start -->
<div class="page-contentbar">

<!--left navigation start-->
<aside class="sidebar-navigation">
    <div class="scrollbar-wrapper">
        <div>
            <button type="button" class="button-menu-mobile btn-mobile-view visible-xs visible-sm"> <i class="mdi mdi-close"></i> </button>
            <!-- User Detail box -->
            <div class="user-details">
                <div class="pull-left"> <img src="<?php echo base_url($this->session->userdata('kas_user_pics')); ?>" alt="user" class="thumb-md img-circle"> </div>
                <div class="user-info"> <a href="<?php echo base_url('profile'); ?>"><?php echo $this->session->userdata('kas_user_othername').' '.$this->session->userdata('kas_user_lastname'); ?></a>
                    <p class="text-muted m-0">
						<?php echo $this->session->userdata('kas_user_role'); ?> 
                        <a href="<?php echo base_url('logout'); ?>" class="pull-right" title="Logout"><i class="ti-power-off"></i></a>
                    </p>
                </div>
            </div>
            <!--- End User Detail box --> 
            
            <!-- Left Menu Start -->
            <ul class="metisMenu nav" id="side-menu">
                <?php $role = $this->session->userdata('kas_user_role'); ?>
                <?php $admin_mod = array('ad_country', 'ad_state', 'ad_personal', 'ad_contribution', 'ad_vault', 'ad_bank'); ?>
				<?php if($role == 'Admin'){ ?>
                <li> <a href="javascript:;" aria-expanded="true" class="<?php if(in_array($page_active, $admin_mod)){echo 'active';} ?>"><i class="mdi mdi-settings"></i> Administration <span class="fa arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">
                    	<li><a href="<?php echo base_url('admin/country'); ?>" class="<?php echo $ad_country_act; ?>">Country</a></li>
                        <li><a href="<?php echo base_url('admin/state'); ?>" class="<?php echo $ad_state_act; ?>">State</a></li>
                        <li><a href="<?php echo base_url('admin/user'); ?>" class="<?php echo $ad_user_act; ?>">Users</a></li>
                        <li><a href="<?php echo base_url('admin/personal'); ?>" class="<?php echo $ad_personal_act; ?>">Personal Savings</a></li>
                        <li><a href="<?php echo base_url('admin/contribution'); ?>" class="<?php echo $ad_contribute_act; ?>">Contributions</a></li>
                        <li><a href="<?php echo base_url('admin/vault'); ?>" class="<?php echo $ad_vault_act; ?>">Vaults</a></li>
                        <li><a href="<?php echo base_url('admin/bank'); ?>" class="<?php echo $ad_bank_act; ?>">Banks</a></li>
                        <li><a href="<?php echo base_url('admin/transaction'); ?>" class="<?php echo $ad_transaction_act; ?>">Transactions</a></li>
                        <li><a href="<?php echo base_url('admin/offer'); ?>" class="<?php echo $ad_offer_act; ?>">Offers</a></li>
                    </ul>
                </li>
                <?php } ?>
                
                <li><a href="<?php echo base_url('dashboard'); ?>" class="<?php echo $dash_act; ?>"><i class="mdi mdi-home"></i> Dashboard </a></li>
                
                <li> <a href="javascript:;" aria-expanded="true" class="<?php if($page_active=='saving' || $page_active=='personal' || $page_active=='contribution'){echo 'active';} ?>"><i class="mdi mdi-cash"></i> Savings <span class="fa arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">
                        <li><a href="<?php echo base_url('savings/personal'); ?>" class="<?php echo $personal_act; ?>">Personal</a></li>
                        <li><a href="<?php echo base_url('savings/contribution'); ?>">Contributions</a></li>
                    </ul>
                </li>
                
                <li><a href="<?php echo base_url('accounts'); ?>" class="<?php echo $account_act; ?>"><i class="mdi mdi-bank"></i> Accounts </a></li>
                
                <li><a href="<?php echo base_url('offer/lists'); ?>" class="<?php echo $offer_act; ?>"><i class="mdi mdi-gift"></i> Offers </a></li>
                
                <li><a href="<?php echo base_url('vaults'); ?>" class="<?php echo $vault_act; ?>"><i class="mdi mdi-wallet"></i> Vault/Wallet </a></li>
            </ul>
        </div>
    </div>
    <!--Scrollbar wrapper--> 
</aside>
<!--left navigation end--> 

<!-- START PAGE CONTENT -->
<div id="page-right-content">
