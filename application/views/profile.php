<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="p-0 text-center">
                <div class="member-card">
                    <div class="thumb-xl member-thumb m-b-10 center-block"> <img src="<?php echo base_url($profile_pics); ?>" class="img-circle img-thumbnail" alt="user"> <?php if($profile_activate == 1){echo '<i class="mdi mdi-checkbox-marked-circle-outline member-star text-primary" title="Verified User"></i>';} else {echo '<i class="mdi mdi-close-circle-outline member-star text-danger" title="Unverified User"></i>';} ?> </div>
                    <div class="">
                        <h4 class="m-b-5"><?php echo $profile_othername.' '.$profile_lastname; ?></h4>
                        <p class="text-muted">Since <?php echo date(fd_date, strtotime($profile_reg_date)).' <span class="small text-primary">('.$profile_reg_ago.' ago)</span>'; ?></p>
                    </div>
                    <p class="text-info m-t-10 small"><b>== Last Log: <?php echo date('d M, Y h:iA', strtotime($profile_lastlog)); ?> ==</b></p>
                </div>
            </div>
            <!-- end card-box --> 
            
        </div>
        <!-- end col --> 
    </div>
    <!-- end row -->
    
    <div class="m-t-30">
        <ul class="nav nav-tabs tabs-bordered">
            <li class="active"> <a href="#home-b1" data-toggle="tab" aria-expanded="true"> Profile </a> </li>
            <?php if($public == FALSE){ ?>
            <li class=""> <a href="#profile-b1" data-toggle="tab" aria-expanded="false"> Settings </a> </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="home-b1">
                <?php if(!empty($err_msg)){echo $err_msg;} ?>
                <div class="row">
                    <div class="col-md-4"> 
                        <!-- Personal-Information -->
                        <div class="panel panel-default panel-fill">
                            <div class="panel-heading">
                                <h3 class="panel-title">Personal Information</h3>
                            </div>
                            <div class="panel-body">
                                <div class="m-b-20"> <strong>Full Name</strong> <br>
                                    <p class="text-muted"><?php echo $profile_othername.' '.$profile_lastname; ?></p>
                                </div>
                                <div class="m-b-20"> <strong>Mobile</strong> <br>
                                    <p class="text-muted"><?php echo $profile_phone; ?></p>
                                </div>
                                <div class="m-b-20"> <strong>Email</strong> <br>
                                    <p class="text-muted"><?php echo $profile_email; ?></p>
                                </div>
                                <div class="about-info-p m-b-0"> <strong>Location</strong> <br>
                                    <p class="text-muted"><?php if($profile_address != ''){echo $profile_address.'<br />';} echo $profile_state; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Personal-Information --> 
                    </div>
                    <div class="col-md-8"> 
                        <!-- Personal-Information -->
                        <div class="panel panel-default panel-fill">
                            <div class="panel-heading">
                                <h3 class="panel-title">Biography</h3>
                            </div>
                            <div class="panel-body">
                                <h5 class="header-title text-uppercase">About</h5>
                                <p><?php echo $profile_bio; ?></p><br />
                                
                                <div class="m-b-20"> <strong>Sex</strong> <br>
                                    <p class="text-muted"><?php echo $profile_sex; ?></p>
                                </div>
                                <div class="m-b-20"> <strong>DOB</strong> <br>
                                    <p class="text-muted"><?php if($profile_dob != ''){echo date('d F', strtotime($profile_dob));} else {echo '...';} ?></p>
                                </div>
                                <div class="m-b-20"> <strong>Marital Status</strong> <br>
                                    <p class="text-muted"><?php echo $profile_marital; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Personal-Information --> 
                        
                    </div>
                </div>
            </div>
            <?php if($public == FALSE){ ?>
            <div class="tab-pane" id="profile-b1"> 
                <!-- Personal-Information -->
                <div class="panel panel-default panel-fill">
                    <div class="panel-heading">
                        <h3 class="panel-title">Edit Profile</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open_multipart('profile'); ?>
                            <?php
								$state_list = '';
								if(!empty($allstates)){
									foreach($allstates as $state){
										if($profile_state_id == $state->id){$s_sel = 'selected="selected"';} else {$s_sel = '';}
										$state_list .= '<option value="'.$state->id.'" '.$s_sel.'>'.$state->name.'</option>';	
									}
								}
							?>
                            
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>" />
                                    <label for="othername">First Name</label>
                                    <input type="text" value="<?php echo $profile_othername; ?>" id="othername" name="othername" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" value="<?php echo $profile_lastname; ?>" id="lastname" name="lastname" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" value="<?php echo $profile_email; ?>" id="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" value="<?php echo $profile_phone; ?>" id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <?php
										if(!empty($profile_sex)){
											if($profile_sex == 'Male'){$s1 = 'selected="selected"';} else {$s1 = '';}
											if($profile_sex == 'Female'){$s2 = 'selected="selected"';} else {$s2 = '';}
										} else {$s1 = ''; $s2 = '';}
									?>
                                    <label for="sex">Sex</label><br />
                                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="sex" name="sex">
                                        <option></option>
                                        <option value="Male" <?php echo $s1; ?>>Male</option>
                                        <option value="Female" <?php echo $s2; ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="dob">DOB</label>
                                    <input class="form-control" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" id="datepicker-autoclose" type="text" id="dob" name="dob" value="<?php echo $profile_dob; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo $profile_address; ?></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="state">State</label><br />
                                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="state" name="state">
                                        <option></option>
										<?php echo $state_list; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <?php
										if(!empty($profile_sex)){
											if($profile_marital == 'Single'){$m1 = 'selected="selected"';} else {$m1 = '';}
											if($profile_marital == 'Married'){$m2 = 'selected="selected"';} else {$m2 = '';}
											if($profile_marital == 'Divorced'){$m3 = 'selected="selected"';} else {$m3 = '';}
										} else {$m1 = ''; $m2 = ''; $m3 = '';}
									?>
                                    <label for="marital">Marital Status</label><br />
                                    <select class="select2 form-control select2-hidden-accessible" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true" style="width:100%;" id="marital" name="marital">
                                        <option></option>
                                        <option value="Single" <?php echo $m1; ?>>Single</option>
                                        <option value="Married" <?php echo $m2; ?>>Married</option>
                                        <option value="Divorced" <?php echo $m3; ?>>Divorced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="bio">About Me</label>
                                    <textarea style="height: 125px" id="bio" name="bio" class="form-control"><?php echo $profile_bio; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-xs-12">
                            	<h4 class="text-muted">Change Profile Picture<hr /></h4>
                            </div>
							<div class="col-xs-12 col-sm-6">
                            	<div class="form-group text-center">
                                    <img alt="photo" src="<?php echo base_url($profile_pics); ?>" style="max-width:100%;" /><br />
                                    <input class="filestyle" data-buttonname="btn-primary" id="filestyle-0" name="pics" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                                    <!--<input data-buttonname="btn-primary" id="pics" name="pics" type="file">-->
                                </div>
                           	</div>
                            
                            <div class="col-xs-12">
                            	<h4 class="text-muted">Change Password<hr /></h4>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            	<div class="form-group">
                                    <label for="old">Current Password</label>
                                    <input type="password" id="old" name="old" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                            	<div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="password" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="confirm">Confirm New Password</label>
                                    <input type="password" id="confirm" name="confirm" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-xs-12">
                            	<hr />
                                <button class="btn btn-primary waves-effect waves-light w-md" type="submit"><i class="mdi mdi-content-save-all"></i> Save Record</button>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <!-- Personal-Information --> 
            </div>
            <?php } ?>
        </div>
    </div>
</div>
