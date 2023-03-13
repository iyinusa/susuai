<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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

<!-- Bootstrap core CSS -->
<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="<?php echo base_url(); ?>assets/css/metisMenu.min.css" rel="stylesheet">
<!-- Icons CSS -->
<link href="<?php echo base_url(); ?>assets/css/icons.css" rel="stylesheet">
<!-- Custom styles for this template -->
<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
<style>
	.fblog {padding:10px 20px; margin:10px auto; background-color:#00328B; outline:none; text-decoration:none; color:#fff; font-weight:bold; border-radius:10px;}
	.fblog:hover {background-color:#458BC4;}
</style>

</head>

<body style="background-image:url(<?php echo base_url('landing/img/bg.jpg'); ?>)">
<!--<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '398553420496868',
      xfbml      : true,
      version    : 'v2.8'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>-->
<!-- HOME -->
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wrapper-page">
                    <div class="m-t-40 card-box" style="background: rgba(255, 255, 255, 0.85);">
                        <div class="text-center">
                            <h2 class="text-uppercase m-t-0 m-b-30"> <a href="<?php echo base_url(); ?>" class="text-success"> <span><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="" height="30"></span> </a> </h2>
                            <?php if($this->session->userdata('ka_ref_sender') != ''){ // only show if going from bot ?>
                            <a href="<?php echo $authUrl; ?>" class="fblog"><i class="ti-facebook"></i> Continue With Facebook</a>
                            <?php } ?>
                            <hr />
                            <?php if(!empty($err_msg)){echo $err_msg;} ?>
                        </div>
                        <div class="account-content">
                            <?php echo form_open('login', array('class'=>'form-horizontal')); ?>
                                <div class="form-group m-b-20">
                                    <div class="col-xs-12">
                                        <label for="email">Email address</label>
                                        <input class="form-control" type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo $reset ? '' : set_value('email'); ?>">
                                    </div>
                                </div>
                                <div class="form-group m-b-20">
                                    <div class="col-xs-12"> <a href="<?php echo base_url('forgot'); ?>" class="text-muted pull-right font-14"><i class="mdi mdi-key"></i> Forgot your password?</a>
                                        <label for="password">Password</label>
                                        <input class="form-control" type="password" required id="password" name="password" placeholder="Enter your password">
                                    </div>
                                </div>
                                <div class="form-group m-b-30">
                                    <div class="col-xs-12">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox5" type="checkbox">
                                            <label for="checkbox5"> Remember me </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group account-btn text-center m-t-10">
                                    <div class="col-xs-12">
                                        <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="mdi mdi-key"></i> Sign In</button>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- end card-box-->
                    
                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted">Don't have an account? <a href="<?php echo base_url('register'); ?>" class="text-dark m-l-5"><i class="mdi mdi-key-plus"></i> Sign Up</a></p>
                        </div>
                    </div>
                </div>
                <!-- end wrapper --> 
                
            </div>
        </div>
    </div>
</section>
<!-- END HOME --> 

<!-- js placed at the end of the document so the pages load faster --> 
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/metisMenu.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/jquery.slimscroll.min.js"></script> 

<!-- App Js --> 
<script src="<?php echo base_url(); ?>assets/js/jquery.app.js"></script>
</body>
</html>