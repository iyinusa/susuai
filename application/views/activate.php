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
                                <a href="<?php echo base_url('login'); ?>" class="text-success">
                                    <span><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="" height="30"></span>
                                </a>
                            </h2>
                        </div>
                        <div class="account-content">
                            <div class="text-center m-b-20">
                                <img src="<?php echo base_url(); ?>assets/images/success.svg" title="invite.svg" height="80" class="m-t-10">
                                <p class="text-muted m-t-30 line-h-24"> <?php echo $err_msg; ?> </p>
                            </div>

                            <div class="row m-t-30">
                                <div class="col-xs-12">
                                    <a href="<?php echo base_url('login'); ?>" class="btn btn-lg btn-primary btn-block" type="submit"><i class="mdi mdi-key"></i> Sign in</a>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <!-- end card-box-->

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