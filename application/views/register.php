<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $messenger_unique = rand(); ?>
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

<script>
 window.fbAsyncInit = function() {
    FB.init({
      appId      : '398553420496868',
      xfbml      : true,
      version    : 'v2.8'
    });
	
	FB.Event.subscribe('messenger_checkbox', function(e) {
		console.log("messenger_checkbox event");
		console.log(e);

		if (e.event == 'rendered') {
			console.log("Plugin was rendered");
		} else if (e.event == 'checkbox') {
			var checkboxState = e.state;
			console.log("Checkbox state: " + checkboxState);
			document.getElementById('opt_in').value = checkboxState; //track opt-in state
		} else if (e.event == 'not_you') {
			console.log("User clicked 'not you'");
		} else if (e.event == 'hidden') {
			console.log("Plugin was hidden");
		}
	});
  };

  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk')
  );
  
  function confirmOptIn() {
		FB.AppEvents.logEvent('MessengerCheckboxUserConfirmation', null, {
			'app_id':'398553420496868',
			'page_id':'420086041674286',
			'ref':'Hello',
			'user_ref':'<?php echo $messenger_unique; ?>'
		});
	}
</script>

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
                            <?php if(!empty($err_msg)){echo $err_msg;} else {echo '<h4 class="text-muted">CREATE ACCOUNT</h4>';} ?>
                        </div>
                        <div class="account-content">
                            <?php echo form_open('register', array('class'=>'form-horizontal')); ?>

                                <div class="form-group m-b-20">
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="firstname">First Name</label>
                                        <input class="form-control" type="text" id="firstname" name="firstname" required placeholder="Your firstname" value="<?php echo $reset ? '' : set_value('firstname'); ?>">
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="lastname">Last Name</label>
                                        <input class="form-control" type="text" id="lastname" name="lastname" required placeholder="Your lastname" value="<?php echo $reset ? '' : set_value('lastname'); ?>">
                                    </div>
                                </div>

                                <div class="form-group m-b-20">
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="email">Email address</label>
                                        <input class="form-control" type="email" id="email" name="email" required placeholder="Your email" value="<?php echo $reset ? '' : set_value('email'); ?>">
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="phone">Phone number</label>
                                        <input class="form-control" type="phone" id="phone" name="phone" required placeholder="Your phone" value="<?php echo $reset ? '' : set_value('phone'); ?>">
                                    </div>
                                </div>

                                <div class="form-group m-b-20">
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="password">Password</label>
                                        <input class="form-control" type="password" required id="password" name="password" placeholder="Your password">
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <label for="confirm">Confirm Password</label>
                                        <input class="form-control" type="password" required id="confirm" name="confirm" placeholder="Confirm your password">
                                    </div>
                                </div>

                                <div class="form-group m-b-30">
                                    <div class="col-xs-12">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox5" type="checkbox" required>
                                            <label for="checkbox5">
                                                I accept <a href="<?php echo base_url('terms'); ?>" class="small">Terms and Conditions</a> and <a href="<?php echo base_url('privacy'); ?>" class="small">Privacy Policy</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row alert alert-info">
                                    <div class="col-xs-12">
                                        <b>Sync with Messenger:</b>
                                        <div class="form-group">
                                            <div class="fb-messenger-checkbox" 
                                                origin=https://susu-ai.com 
                                                page_id=420086041674286 
                                                messenger_app_id=398553420496868 
                                                user_ref="<?php echo $messenger_unique; ?>" 
                                                prechecked="true" 
                                                allow_login="true" 
                                                size="large"></div>
                                        </div>
                                        <input type="button" onclick="confirmOptIn()" value="CLICK to Opt-In First" class="btn btn-success btn-sm" /> <small class="text-danger"><b>If UN-CHECKED and you don't CLICK THIS, you will not be able to manage your account via Messenger!</b></small>
                                        <input type="hidden" id="opt_in" name="opt_in" />
                                        <input type="hidden" id="opt_id" name="opt_id" value="<?php echo $messenger_unique; ?>" />
                                    </div>
                                </div>

                                <div class="form-group account-btn text-center m-t-10">
                                    <div class="col-xs-12">
                                        <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="mdi mdi-key-plus"></i> Sign Up Free</button>
                                    </div>
                                </div>

                            </form>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <!-- end card-box-->


                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted">Already have an account?  <a href="<?php echo base_url('login'); ?>" class="text-dark m-l-5"><i class="mdi mdi-key"></i> Sign In</a></p>
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