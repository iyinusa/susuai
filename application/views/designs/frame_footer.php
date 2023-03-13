
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
</body></html>