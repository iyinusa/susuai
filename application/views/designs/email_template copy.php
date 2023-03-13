<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title></title>
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

<body>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="m-t-40 card-box">
                        <div class="text-center">
                            <h2 class="text-uppercase m-t-0 m-b-30">
                                <a href="<?php echo base_url(); ?>" class="text-success">
                                    <span><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="<?php echo app_name; ?>" height="30"></span>
                                </a>
                                <hr/>
                                <?php echo $subhead; ?>
                                <hr/>
                            </h2>
                        </div>
                        <div class="account-content">
                            <div class="text-center m-b-20">
                                <p class="text-muted m-b-0 line-h-24">
                                	<?php echo $message; ?>
                                </p>
                            </div>
                            
                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <!-- end card-box-->


                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted"><?php echo app_name; ?> Team</p>
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