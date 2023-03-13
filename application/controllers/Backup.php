<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->load->dbutil();
		$this->load->library('email');
    }
	
	public function index() {
		$prefs = array(     
        	'format'      => 'zip',             
          	'filename'    => 'db_backup.sql'
        );


        $backup =& $this->dbutil->backup($prefs); 

        $db_name = 'backup-on-'. date("Y-m-d-H-i-s") .'.zip';
        $save = 'assets/backups/'.$db_name;

        $this->load->helper('file');

        if(write_file($save, $backup)) {
        	$status = 'BackUp Successful!';
        } else {
        	$status = 'BackUp Failed!';
        }

        // try and push email notification
        $this->email->clear(); //clear initial email variables
		$this->email->to('iyinusa@yahoo.co.uk');
		$this->email->from('webmaster@susu-ai.com','SusuAI');
		$this->email->subject('SusuAI'.' - Database BackUp');
		$this->email->attach($save); // attach zip file to email
						
		//compose html body of mail
		$mail_subhead = 'Backup Notification';
		$body_msg = '
			SusuAI Database Backup ('.$db_name.') Status:<br /><br />
			<b>Local Storage: </b>'.$status.'<br /><br />
			<div class="mbtn"><a href="https://susu-ai.com/'.$save.'" class="btn btn-primary">Click to Download Database</a></div> or copy and paste below link in browser, if above not working<br /><br/>https://susu-ai.com/'.$save.'<br /><br/>Thanks
		';
						
		$mail_data = array('message'=>$body_msg, 'subhead'=>$mail_subhead);
		$this->email->set_mailtype("html"); //use HTML format
		$mail_design = $this->load->view('designs/email_template', $mail_data, TRUE);
				
		$this->email->message($mail_design);
		if($this->email->send()) {}

        // force download backup
        //$this->load->helper('download');
        //force_download($db_name, $backup);
	}
}
