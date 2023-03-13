<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="header-title m-t-0">Notifications</h4>
            <p class="text-muted m-b-30">
                Don't miss out anything.
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="timeline timeline-left">
                <article class="timeline-item alt">
                    <div class="text-left">
                        <div class="time-show first">
                            <a href="javascript:;" class="btn btn-custom">Most Recents</a>
                        </div>
                    </div>
                </article>
                
                <?php
                	if(!empty($allnotify)){
						foreach($allnotify as $notify){
							$id = $notify->id;
							$nhash = $notify->nhash;
							$item_id = $notify->item_id;
							$item = $notify->item;
							$new = $notify->new;
							$title = $notify->title;
							$details = $notify->details;
							$type = $notify->type;
							$reg_date = $notify->reg_date;
							
							$reg_date_stamp = timespan(strtotime($reg_date), time());
							$reg_date_stamp = explode(',', $reg_date_stamp);
							
							// identify notification
							if($item == 'personal'){
								$item_icon = 'mdi mdi-cash';
								$item_icon_color = 'bg-success';
							} else if($item == 'vault'){
								$item_icon = 'mdi mdi-wallet';
								$item_icon_color = 'bg-warning';
							} else {
								$item_icon = 'mdi mdi-information';
								$item_icon_color = 'bg-primary';
							}
							
							if($new == 1){$alert = 'danger';} else {$alert = 'default';}
							
							echo '
								<article class="timeline-item bg-'.$alert.'">
									<div class="timeline-desk">
										<div class="panel">
											<div class="timeline-box">
												<span class="arrow"></span>
												<span class="timeline-icon '.$item_icon_color.'"><i class="'.$item_icon.'"></i></span>
												<h4 class="text-success"><a href="'.base_url('notifications/v/'.$nhash).'" class="btn btn-default btn-sm pull-right"><i class="mdi mdi-eye"></i> See now</a> '.$reg_date_stamp[0].' ago</h4>
												<p class="timeline-date text-muted"><small>'.date('d M, Y h:i:sa', strtotime($reg_date)).'</small></p>
												<p>'.$details.'...</p>
				
											</div>
										</div>
									</div>
								</article>
							';
						}
					} else {
						echo '
							<article class="timeline-item">
								<div class="timeline-desk">
									<div class="panel">
										<div class="timeline-box">
											<span class="arrow"></span>
											<span class="timeline-icon"><i class="mdi mdi-checkbox-blank-circle-outline"></i></span>
											<p>You have no notification yet</p>
										</div>
									</div>
								</div>
							</article>
						';
					}
                ?>

            </div>
        </div>
    </div>
    <!--end row -->
</div>
