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
</head>

<body style="background-image:url(<?php echo base_url('landing/img/bg.jpg'); ?>)">
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="m-t-40 card-box" style="background: rgba(255, 255, 255, 0.85);">
                        <div class="text-center">
                            <h2 class="text-uppercase m-t-0 m-b-30">
                                <a href="<?php echo base_url(); ?>" class="text-success">
                                    <span><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="" height="30"></span>
                                </a>
                            </h2>
                        </div>
                        <div class="account-content">
                            <div class="text-center m-b-20">
                                <p class="text-muted m-b-0 line-h-24">
                                	<?php if($change == FALSE){$link = ''; ?>
                                    Enter your email address and we'll send you an email with instructions to reset your password.
                                    <?php } else {$link = 'change/'.$param1.'/'.$param2; ?> 
                                    Create new password for your account
                                    <?php } ?> 
                                </p>
                            </div>
                            
                            <?php if(!empty($err_msg)){echo $err_msg;} ?>

                            <?php echo form_open('forgot/'.$link, array('class'=>'form-horizontal')); ?>
							<?php if($change == FALSE){ ?>
                                <div class="form-group m-b-20">
                                    <div class="col-xs-12">
                                        <label for="email">Email address</label>
                                        <input class="form-control" type="email" id="email" name="email" required placeholder="john@deo.com">
                                    </div>
                                </div>

                                <div class="form-group account-btn text-center m-t-10">
                                    <div class="col-xs-12">
                                        <button class="btn btn-lg btn-primary btn-block" type="submit" name="btnSend"><i class="mdi mdi-key"></i> Send Reset</button>
                                    </div>
                                </div>
							<?php } else { ?>
                                <div class="form-group m-b-20">
                                    <div class="col-xs-12">
                                        <label for="new">New Password</label>
                                        <input class="form-control" type="password" id="new" name="new" required>
                                    </div>
                                </div>
                                
                                <div class="form-group m-b-20">
                                    <div class="col-xs-12">
                                        <label for="confirm">Confirm New Password</label>
                                        <input class="form-control" type="password" id="confirm" name="confirm" required>
                                    </div>
                                </div>

                                <div class="form-group account-btn text-center m-t-10">
                                    <div class="col-xs-12">
                                        <button class="btn btn-lg btn-primary btn-block" type="submit" name="btnChange"><i class="mdi mdi-key"></i> Reset Password</button>
                                    </div>
                                </div>
                           	<?php } ?>
                            <?php echo form_close(); ?>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <!-- end card-box-->


                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted">Back to <a href="<?php echo base_url('login'); ?>" class="text-dark m-l-5"><i class="mdi mdi-key"></i> Sign In</a></p>
                        </div>
                    </div>

                </div>
                <!-- end wrapper -->

            </div>
        </div>
    </div>
</section>

<!-- js placed at the end of the document so the pages load faster --> 
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/metisMenu.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/jquery.slimscroll.min.js"></script> 

<!-- App Js --> 
<script src="<?php echo base_url(); ?>assets/js/jquery.app.js"></script>
</body>
</html>