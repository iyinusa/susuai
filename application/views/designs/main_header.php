<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
		<title><?php echo $title; ?></title>
		<meta content="<?php echo app_meta_desc; ?>" name="description" />
		<meta content="<?php echo app_name; ?>" name="author" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.png">			
		
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/bootstrap/css/bootstrap.min.css">		
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700%7CLato:300,300i,400,400i,700,700i" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/fonts/font-awesome.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/owlcarousel/css/owl.carousel.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/owlcarousel/css/owl.theme.css">			
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/css/animate.css">	
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/css/jquery.carousel-3d.default.css">	
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/css/style.css">	
        <link rel="stylesheet" href="<?php echo base_url(); ?>landing/css/switcher/switcher.css"> 	
		<link rel="stylesheet" href="<?php echo base_url(); ?>landing/css/switcher/style1.css">	
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	
    <body data-spy="scroll" data-offset="80">
		
        <!-- START PRELOADER -->
		<div class="preloader">
			<div class="status">
				<div class="status-mes"></div>
			</div>
		</div>
		<!-- END PRELOADER -->
		
		<!-- START NAVBAR -->
		<div class="navbar navbar-default navbar-fixed-top menu-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a href="<?php echo base_url(); ?>" class="navbar-brand"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="logo"></a>
				</div>
				<div class="navbar-collapse collapse">
					<nav>
						<ul class="nav navbar-nav navbar-right">
							<?php if($page_active=='main'){ ?>
                            <li><a class="page-scroll" href="#home">Home</a></li>
							<li><a class="page-scroll" href="#feature">Features</a></li>						
							<li><a class="page-scroll" href="#screenshots">Screenshots</a></li>	
                            <?php } ?>
                            <?php if($this->session->userdata('logged_in') == FALSE){ ?>
                            <li><a class="btn btn-primary btn-xs" href="<?php echo base_url('login'); ?>"><i class="fa fa-key"></i> Sign In</a></li>
                            <?php } else { ?>
                            <li><a class="btn btn-success btn-xs" href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <?php } ?>
						</ul>
					</nav>
				</div> 
			</div><!--- END CONTAINER -->
		</div> 
		<!-- END NAVBAR -->
