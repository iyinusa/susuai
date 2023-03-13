<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		echo phpinfo();
	}
}
