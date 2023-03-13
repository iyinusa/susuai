<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Funding Notification</h4>
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
                <a href="<?php echo base_url('vaults'); ?>" class="btn btn-success"><i class="mdi mdi-wallet"></i> Vault</a>
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
