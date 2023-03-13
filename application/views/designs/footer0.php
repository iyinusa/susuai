<div class="footer">
    <div class="pull-right hidden-xs"> Built with <i class="mdi mdi-heart-outline text-danger"></i> in Nigeria </div>
    <div> <strong><?php echo app_name; ?></strong> - Copyright &copy; <?php echo date('Y'); ?> </div>
</div>
<!-- end footer -->

</div>
<!-- End #page-right-content -->

</div>
<!-- end .page-contentbar -->
</div>
<!-- End #page-wrapper --> 

<!-- js placed at the end of the document so the pages load faster --> 
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/metisMenu.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/jquery.slimscroll.min.js"></script> 

<?php if($page_active != 'dashboard'){ ?>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/parsleyjs/parsley.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/clockpicker/js/bootstrap-clockpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.js"></script>

<!-- Datatable js -->
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.colVis.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>

<!-- init -->
<script src="<?php echo base_url(); ?>assets/pages/jquery.datatables.init.js"></script>

<!-- KNOB JS -->
<!--[if IE]>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/jquery-knob/excanvas.js"></script>
<![endif]-->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-knob/jquery.knob.js"></script>
<script type="text/javascript">
	$('[data-plugin="knob"]').each(function(idx, obj) {
		$(this).knob();
	 });
</script>

<!-- form advanced init js -->
<script src="<?php echo base_url(); ?>assets/pages/jquery.form-advanced.init.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.form-validation').parsley();
		$('.summernote').summernote({
			height: 350,                 // set editor height
			minHeight: null,             // set minimum height of editor
			maxHeight: null,             // set maximum height of editor
			focus: false                 // set focus to editable area after initializing summernote
		});
	});
</script>
<?php } ?>

<?php if($page_active == 'dashboard'){ ?>
<!--Morris Chart-->
<script src="<?php echo base_url(); ?>assets/plugins/morris/morris.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/raphael/raphael-min.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/jquery.morris.init.js"></script>

<!-- KNOB JS -->
<!--[if IE]>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/jquery-knob/excanvas.js"></script>
<![endif]-->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-knob/jquery.knob.js"></script>
<script type="text/javascript">
	$('[data-plugin="knob"]').each(function(idx, obj) {
		$(this).knob();
	 });
</script>
<?php } ?>

<!-- App Js --> 
<script src="<?php echo base_url(); ?>assets/js/jquery.app.js"></script>

<script type="text/javascript">
	function ps_cal(){
		var curr			= '&#8358;';
		var get_contribute 	= 0;
		var duration_text	= '';
		var name 		= document.getElementById('name').value;
		var target 		= document.getElementById('target').value;
		var duration 	= document.getElementById('duration').value;
		var contribute 	= document.getElementsByName('contribute');
		var contribute_value;
		for(var i = 0; i < contribute.length; i++){
			if(contribute[i].checked){
				contribute_value = contribute[i].value;
			}
		}
		
		get_contribute = target / duration;
		
		if(contribute_value == 'Monthly'){
			if(duration > 1){duration_text = duration+' Months';} else {duration_text = duration+' Month';}
		} else if(contribute_value == 'Weekly'){
			if(duration > 1){duration_text = duration+' Weeks';} else {duration_text = duration+' Week';}	
		} else {
			if(duration > 1){duration_text = duration+' Days';} else {duration_text = duration+' Day';}
		}
		
		document.getElementById('contribute_amt').value = get_contribute;
		
		get_contribute = parseFloat(get_contribute).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		target = parseFloat(target).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById('ps_msg').innerHTML = '<div class="col-xs-12"><div class="alert alert-info fade in text-center" role="alert"><div style="font-weight:bold; font-size:14px;">Starting <b style="color:red;">'+name.toUpperCase()+'</b> savings with '+curr+target+' budget! Will take about <b style="color:red;">'+duration_text+'</b> to complete cycle if doing <b style="color:red;">'+curr+get_contribute+' '+contribute_value+'</b> contribution</div></div></div>';
	}
	
	function toggle() {
		var x = document.getElementById('toggle_me');
		if (x.style.display === 'none') {
			x.style.display = 'block';
			document.getElementById('togglebtn').innerHTML = '- Cancel';
		} else {
			x.style.display = 'none';
			document.getElementById('togglebtn').innerHTML = '- Change';
		}
	}
	
	function toggle2() {
		var y = document.getElementById('toggle_other');
		if (y.style.display === 'none') {
			y.style.display = 'block';
			document.getElementById('togglevalue').value = 1;
		} else {
			y.style.display = 'none';
			document.getElementById('togglevalue').value = 0;
		}
	}
</script>

<script type="text/javascript">
	function get_verify_acc(){
		var hr = new XMLHttpRequest();
		var vbank = document.getElementById('vbank').value;
		var vacc_no = document.getElementById('vacc_no').value;
		var c_vars = "bank="+vbank+"&acc_no="+vacc_no;
		hr.open("POST", "<?php echo base_url('savings/validate_account'); ?>", true);
		hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		hr.onreadystatechange = function() {
			if(hr.readyState == 4 && hr.status == 200) {
				var return_data = hr.responseText;
				document.getElementById("vacc_name").value = return_data;
		   }
		}
		hr.send(c_vars);
		document.getElementById("vacc_name").value = 'Validating...';
	}
</script>

<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			
		  ga('create', 'UA-93997210-1', 'auto');
		  ga('send', 'pageview');
		
		</script>
</body></html>