<!-- START HOME -->
<section data-stellar-background-ratio="0.3" id="home" class="home_bg" style="background-image: url(<?php echo base_url(); ?>landing/img/bg.jpg);  background-size:cover; background-position: center center;">
    <div class="container">
        <div class="row">
          <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="hero-text">
                <h2>Hi! I'm <?php echo app_name; ?>, your Artificial Intelligence Savings Planner</h2>
                 <p>Tell me your savings plan, and watch me automate it for you</p>
                <div class="home_btn">
                    <?php if($this->session->userdata('logged_in') == FALSE){ ?>
                    <a href="<?php echo base_url('login'); ?>" class="app-btn wow bounceIn" data-wow-delay=".6s" ><i class="fa fa-key"></i> Sign In</a>
                    <a href="<?php echo base_url('register'); ?>" class="app-btn wow bounceIn" data-wow-delay=".8s" ><i class="fa fa-user"></i> Create Account</a>
                    <?php } else { ?>
                    <a href="<?php echo base_url('dashboard'); ?>" class="app-btn wow bounceIn" data-wow-delay=".6s" ><i class="fa fa-home"></i> Dashboard</a>
                    <?php } ?>
                </div>
            </div> 
          </div><!--- END COL -->	
          <div class="col-md-4 col-sm-12 col-xs-12 text-center">
            <div class="hero-text-img">
                <img src="<?php echo base_url(); ?>landing/img/iphone_img.png" alt="" />
            </div>
          </div><!--- END COL -->			  
        </div><!--- END ROW -->
    </div><!--- END CONTAINER -->
</section>
<!-- END  HOME -->	

<!-- START ABOUT -->
<section id="feature" class="about-content section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="single_about">
                    <i class="fa fa-check"></i>
                    <h4>Setup Account</h4>
                    <p>Your first step is just to create an account or sign in if already have one in just few clicks. </p>
                </div>
            </div><!-- END COL-->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="single_about">
                    <i class="fa fa-money"></i>
                    <h4>Create Savings Plan</h4>
                    <p>Tell me your target and duration for savings, I will calculate your daily, weekly or monthly contributions.</p>
                </div>
            </div><!-- END COL-->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="single_about">
                    <i class="fa fa-paper-plane-o"></i>
                    <h4>I Will Automate</h4>
                    <p>Watch me help you auto save based on plans, and Credit your specified beneficiary on completion.</p>
                </div>
            </div><!-- END COL-->
        </div><!-- END ROW-->
    </div><!-- END CONTAINER-->
</section>
<!-- END ABOUT -->

<!-- START FEATURED ONE -->
<section class="feature-one section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-7 col-xs-12 hidden-xs">
                <div class="single_feature_img" style="background-color:#000;">
                    <video width="100%" height="550" autoplay loop>
                        <source src="<?php echo base_url('susuai_screencast.mp4'); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div><!-- END COL-->
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="single_feature_one">
                    <h3>Meet me on <br> <strong>Messenger</strong></h3>
                    <p>You can easily chat me (SusuAIBot) on Messager and I will get the rest done for you. </p>
                    <h4><a class="single_feature_btn_light" href="https://m.me/susuaibot"><img alt="Meet Me On Messenger" src="<?php echo base_url('landing/img/facebook.png'); ?>" /></a></h4>
                </div>
            </div><!-- END COL-->
        </div><!-- END ROW-->
    </div><!-- END CONTAINER-->
</section>
<!-- END FEATURED ONE -->

<!-- START MAIN FEATURES -->
<section class="why_choose_us section-padding">
    <div class="container">
        <div class="row">
            <div class="section-title text-center wow zoomIn">
                <h2>Features</h2>
                <div class="line"></div>
                <p>I've got you covered on all your savings plans such as House Rent, Buy Car, Pay Bills, etc.</p>						
            </div>	
            <div class="col-md-6 col-sm-6 col-xs-12"> 
                <div class="feature_img">
                    <img class="img-responsive wow bounceIn" data-wow-delay=".6s" src="<?php echo base_url(); ?>landing/img/mockup3-phone.png" alt="">
                </div>
            </div><!--- END COL -->					
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div id="why_choose">
                    <!-- Wrapper for slides -->
                    <div class="row ">							
                        <div class="col-sm-6">
                            <div class="single_feature">
                                <i class="fa fa-money"></i>
                                <h3>Create Plan</h3>
                                <span></span>
                                <p>Create savings plan and specify beneficiary once savings is complete. I will take care of the rest.</p>
                            </div>
                        </div><!-- END COL-->	
                        <div class="col-sm-6">
                            <div class="single_feature">
                                <i class="fa fa-bank"></i>
                                <h3>Beneficials</h3>
                                <span></span>
                                <p>Link and manage account to be credited when savings is completed, once linked, I will do the rest for you.</p>	
                            </div>
                        </div><!-- END COL-->								
                        <div class="col-sm-6">
                            <div class="single_feature">
                                <i class="fa fa-cc-mastercard"></i>
                                <h3>Savings</h3>
                                <span></span>
                                <p>I will auto perform savings based on scheduled plan, all you have to do is just to monitor the savings. </p>	
                            </div>
                        </div><!-- END COL-->					
                        <div class="col-sm-6">
                            <div class="single_feature">
                                <i class="fa fa-paper-plane-o"></i>
                                <h3>Vault</h3>
                                <span></span>
                                <p>You can always keep track of how your funds moved from your Card, to Savings Account, to Beneficiary Account. </p>
                            </div>
                        </div><!-- END COL-->
                    </div><!-- END CAROUSEL INNER -->
                </div><!-- END CAROUSEL SLIDE -->				
            </div><!--- END COL -->					
        </div><!--- END ROW -->			
    </div><!--- END CONTAINER -->		
</section>
<!-- END MAIN FEATURES -->

<!-- START APP SCREENSHOT  -->
<section id="screenshots" class="app-screenshot section-padding">
    <div class="container">
        <div class="row">
            <div class="section-title text-center wow zoomIn">
                <h2>Screenshots</h2>
                <div class="line"></div>
                <p>Awesome and sleek interface.</p>
            </div>				
            <div class="col-md-10 col-md-offset-1">
                <div class="screenshot-carousel" data-carousel-3d>
                    <img src="<?php echo base_url(); ?>landing/img/screenshot/app1.jpg" alt="">
                    <img src="<?php echo base_url(); ?>landing/img/screenshot/app2.jpg" alt="">
                    <img src="<?php echo base_url(); ?>landing/img/screenshot/app3.jpg" alt="">
                    <img src="<?php echo base_url(); ?>landing/img/screenshot/app4.jpg" alt="">
                    <img src="<?php echo base_url(); ?>landing/img/screenshot/app5.jpg" alt="">
                </div>
            </div><!--- END COL -->
        </div><!--- END ROW -->			
    </div><!--- END CONTAINER -->	
</section>
<!-- END APP SCREENSHOT -->

<!-- START TESTIMONIAL -->
<section class="testimonial section-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                <div id="team__carousel" class="carousel slide" data-ride="carousel" data-interval="9999999">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                      <li data-target="#team__carousel" data-slide-to="0" class="active"></li>
                      <li data-target="#team__carousel" data-slide-to="1"></li>
                      <li data-target="#team__carousel" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner text-center">
                        <div class="item active">
                            <div class="testimonial-text">
                                <i class="fa fa-quote-left"></i>
                                <p>"With SusuAI, I worried less about my foreign bills, all I did was to specify my Target and Duration with Beneficiary Account."</p>
                                <img src="<?php echo base_url(); ?>assets/images/users/avatar300.png" class="img-responsive" alt="" />
                                <h4>Tunde Scoot</h4>
                                <h5>Engineer</h5>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimonial-text">
                                <i class="fa fa-quote-left"></i>
                                <p>"Most time I don't remember to pay some bill, but with SusuAI, it will help me sorts all without me been worried."</p>
                                <img src="<?php echo base_url(); ?>assets/images/users/avatar300.png" class="img-responsive" alt="" />
                                <h4>Wale John</h4>
                                <h5>Doctor</h5>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimonial-text">
                                <i class="fa fa-quote-left"></i>
                                <p>"Introducing SusuAI to most of my Clients totally solved my headache in reminding them about their bills, I just got them when time due."</p>
                                <img src="<?php echo base_url(); ?>assets/images/users/avatar300.png" class="img-responsive" alt="" />
                                <h4>Koffi Smith</h4>
                                <h5>Entrepreneur</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--- END COL -->				
        </div><!--- END ROW -->
    </div><!--- END CONTAINER -->	
</section>
<!-- END TESTIMONIAL -->