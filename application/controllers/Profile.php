<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
		}
		
		$data['public'] = FALSE;
		
		if($_POST) {
			$profile_id = $_POST['profile_id'];
			$othername = $_POST['othername'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$phone = $_POST['phone'];
			$sex = $_POST['sex'];
			$dob = $_POST['dob'];
			$address = $_POST['address'];
			$state = $_POST['state'];
			$marital = $_POST['marital'];
			$bio = $_POST['bio'];
			$old = $_POST['old'];
			$password = $_POST['password'];
			$confirm = $_POST['confirm'];
			
			// do profile update here
			$profile_data = array(
				'othername' => $othername,
				'lastname' => $lastname,
				'email' => $email,
				'phone' => $phone,
				'sex' => $sex,
				'dob' => $dob,
				'address' => $address,
				'state' => $state,
				'marital' => $marital,
				'bio' => $bio,
			);
			$profile_upd = $this->Crud->update('id', $profile_id, 'ka_user', $profile_data);
			if($profile_upd > 0) {
				$data['err_msg'] = $this->Crud->msg('success', 'Record Updated');
				//get state name
				$state_name = '';
				$getstate = $this->Crud->read_single('id', $state, 'ka_state');
				if(!empty($getstate)){
					foreach($getstate as $st){
						$state_name = $st->name;	
					}
				}
				// update session data
				$pr_s_data = array (
					'kas_user_email' => $email,
					'kas_user_othername' => $othername,
					'kas_user_lastname' => $lastname,
					'kas_user_dob' => $dob,
					'kas_user_sex' => $sex,
					'kas_user_phone' => $phone,
					'kas_user_address' => $address,
					'kas_user_state_id' => $state,
					'kas_user_state' => $state_name,
					'kas_user_marital' => $marital,
					'kas_user_bio' => $bio
				);
				$this->session->set_userdata($pr_s_data);
			} else {
				$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
			}
			
			// do change profile
			if($old != '' || $password != '' || $confirm != ''){
				if($old == '' || $password == '' || $confirm == ''){
					$data['err_msg'] .= $this->Crud->msg('warning', 'Current, New and Confirm Passwords are required to change password');
				} else if($password != $confirm) {
					$data['err_msg'] .= $this->Crud->msg('warning', 'New and Confirm Paswords not matched');
				} else {
					$username = $this->session->userdata('kas_username');
					if($this->Crud->check2('username', $username, 'password', md5($old), 'ka_user') <= 0) {
						$data['err_msg'] .= $this->Crud->msg('danger', 'Current Password not correct');
					} else {
						$password = md5($password);
						$password_data = array('password'=>$password);
						$password_upd = $this->Crud->update('id', $profile_id, 'ka_user', $password_data);
						if($password_upd > 0){
							$data['err_msg'] .= $this->Crud->msg('success', 'Password changed');
						} else {
							$data['err_msg'] .= $this->Crud->msg('info', 'No Changes to Password');
						}
					}
				}
			}
			
			//check image upload
			if(isset($_FILES['pics']['name'])){
				$log_user_id = $this->session->userdata('kas_id');
				$stamp = time();
				
				$path = 'assets/images/users/'.$log_user_id;
				 
				if (!is_dir($path))
					mkdir($path, 0755);
	
				$pathMain = './assets/images/users/'.$log_user_id;
				if (!is_dir($pathMain))
					mkdir($pathMain, 0755);
	
				$result = $this->do_upload("pics", $pathMain);
	
				if (!$result['status']){
					$data['err_msg'] .= $this->Crud->msg('danger', 'Can not upload picture, try another');
				} else {
					$save_path = $path . '/' . $result['upload_data']['file_name'];
					
					//if size not up to 400px above
					if($result['image_width'] >= 400){
						if($result['image_width'] >= 400 || $result['image_height'] >= 400) {
							if($this->resize_image($pathMain . '/' . $result['upload_data']['file_name'], $stamp .'-400.gif','400','400', $result['image_width'], $result['image_height'])){
								$resize400 = $pathMain . '/' . $stamp.'-400.gif';
								$resize400_dest = $resize400;
								
								if($this->crop_image($resize400, $resize400_dest,'400','220')){
									$save_path400 = $path . '/' . $stamp .'-400.gif';
								}
							}
						}
							
						if($result['image_width'] >= 300 || $result['image_height'] >= 300){
							if($this->resize_image($pathMain . '/' . $result['upload_data']['file_name'], $stamp .'-300.gif','350','350', $result['image_width'], $result['image_height'])){
								$resize100 = $pathMain . '/' . $stamp.'-300.gif';
								$resize100_dest = $resize100;	
								
								if($this->crop_image($resize100, $resize100_dest,'300','300')){
									$save_path100 = $path . '/' . $stamp .'-300.gif';
								}
							}
						}
						
						//save picture in system
						$pics_data = array(
							'user_id' => $log_user_id,
							'pics' => $save_path,
							'pics_small' => $save_path400,
							'pics_square' => $save_path100
						);
						$pics_ins = $this->Crud->create('ka_img', $pics_data);
						// update in user table
						if($pics_ins) {
							$u_pics_data = array('pics'=>$pics_ins);
							$u_pics_ins = $this->Crud->update('id', $profile_id, 'ka_user', $u_pics_data);	
							if($u_pics_ins > 0){
								$data['err_msg'] .= $this->Crud->msg('success', 'Picture Changed');
								// update session
								$u_p_data = array('kas_user_pics' => $save_path100);
								$this->session->set_userdata($u_p_data);
							}
						}
					} else {
						$data['err_msg'] .= $this->Crud->msg('warning', 'Picture be atleast 400px in width');
					}
				}
			}
			/// end profile picture upload
		}
		
		$data['profile_id'] = $this->session->userdata('kas_id');
		$data['profile_username'] = $this->session->userdata('kas_username');
		$data['profile_email'] = $this->session->userdata('kas_user_email');
		$data['profile_lastlog'] = $this->session->userdata('kas_user_lastlog');
		$data['profile_status'] = $this->session->userdata('kas_user_status');
		$data['profile_othername'] = $this->session->userdata('kas_user_othername');
		$data['profile_lastname'] = $this->session->userdata('kas_user_lastname');
		$data['profile_dob'] = $this->session->userdata('kas_user_dob');
		$data['profile_sex'] = $this->session->userdata('kas_user_sex');
		$data['profile_phone'] = $this->session->userdata('kas_user_phone');
		$data['profile_address'] = $this->session->userdata('kas_user_address');
		$data['profile_state_id'] = $this->session->userdata('kas_user_state_id');
		$data['profile_state'] = $this->session->userdata('kas_user_state');
		$data['profile_marital'] = $this->session->userdata('kas_user_marital');
		$data['profile_pics'] = $this->session->userdata('kas_user_pics');
		$data['profile_bio'] = $this->session->userdata('kas_user_bio');
		$data['profile_role'] = $this->session->userdata('kas_user_role');
		$data['profile_activate'] = $this->session->userdata('kas_user_activate');
		$data['profile_reg_date'] = $this->session->userdata('kas_user_reg_date');
		
		$reg_date = timespan(strtotime($this->session->userdata('kas_user_reg_date')), time());
		$reg_date = explode(',', $reg_date);
		$data['profile_reg_ago'] = $reg_date[0];
		
		$data['allstates'] = $this->Crud->read_order('ka_state', 'name', 'ASC');
		$user_page_name = $this->session->userdata('kas_user_othername').' '.$this->session->userdata('kas_user_lastname');
		
		$data['title'] = $user_page_name.' | '.app_name;
		$data['page_active'] = 'profile';
		
		$this->load->view('designs/header', $data);
		$this->load->view('profile', $data);
		$this->load->view('designs/footer', $data);
	}
	
	/////////// ******* VIEW PROFILE **********/////
	public function v($param1) {
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
		}
		
		$user_page_name = 'Public Profile';
		
		if($param1 == ''){
			redirect(base_url(), 'refresh');	
		} else {
			$getuser = $this->Crud->read_single('id', $param1, 'ka_user');
			if(empty($getuser)){
				redirect(base_url(), 'refresh');
			} else {
				$data['public'] = TRUE;
				foreach($getuser as $user){
					$data['profile_id'] = $user->id;
					$data['profile_username'] = $user->username;
					$data['profile_email'] = $user->email;
					$data['profile_lastlog'] = $user->last_log;
					$data['profile_status'] = $user->status;
					$data['profile_othername'] = $user->othername;
					$data['profile_lastname'] = $user->lastname;
					$user_page_name = $user->othername.' '.$user->lastname;
					$data['profile_dob'] = $user->dob;
					$data['profile_sex'] = $user->sex;
					$data['profile_phone'] = $user->phone;
					$data['profile_address'] = $user->address;
					$data['profile_state_id'] = $user->state;
					//get state name
					$state_name = '';
					$getstate = $this->Crud->read_single('id', $user->state, 'ka_state');
					if(!empty($getstate)){
						foreach($getstate as $state){
							$state_name = $state->name;	
						}
					}
					$data['profile_state'] = $state_name;
					$data['profile_marital'] = $user->marital;
					//get logo
					$logo_path = 'assets/images/users/avatar300.png';
					$getimg = $this->Crud->read_single('id', $user->pics, 'ka_img');
					if(!empty($getimg)){
						foreach($getimg as $img){
							$logo_path = $img->pics_square;	
						}
					}
					$data['profile_pics'] = $logo_path;
					$data['profile_bio'] = $user->bio;
					$data['profile_role'] = $user->role;
					$data['profile_activate'] = $user->activate;
					$data['profile_reg_date'] = $user->reg_date;
					$reg_date = timespan(strtotime($user->reg_date), time());
					$reg_date = explode(',', $reg_date);
					$data['profile_reg_ago'] = $reg_date[0];
				}
			}
		}
		
		$data['title'] = $user_page_name.' | '.app_name;
		$data['page_active'] = 'profile';
		
		$this->load->view('designs/header', $data);
		$this->load->view('profile', $data);
		$this->load->view('designs/footer', $data);
	}
	
	function do_upload($htmlFieldName, $path)
    {
        $config['file_name'] = time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|tif|bmp';
        $config['max_size'] = '10000';
        $config['max_width'] = '6000';
        $config['max_height'] = '6000';
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        unset($config);
        if (!$this->upload->do_upload($htmlFieldName))
        {
            return array('error' => $this->upload->display_errors(), 'status' => 0);
        } else
        {
            $up_data = $this->upload->data();
			return array('status' => 1, 'upload_data' => $this->upload->data(), 'image_width' => $up_data['image_width'], 'image_height' => $up_data['image_height']);
        }
    }
	
	function resize_image($sourcePath, $desPath, $width = '500', $height = '500', $real_width, $real_height)
    {
        $this->image_lib->clear();
		$config['image_library'] = 'gd2';
        $config['source_image'] = $sourcePath;
        $config['new_image'] = $desPath;
        $config['quality'] = '100%';
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['thumb_marker'] = '';
		$config['width'] = $width;
        $config['height'] = $height;
		
		$dim = (intval($real_width) / intval($real_height)) - ($config['width'] / $config['height']);
		$config['master_dim'] = ($dim > 0)? "height" : "width";
		
		$this->image_lib->initialize($config);
 
        if ($this->image_lib->resize())
            return true;
        return false;
    }
	
	function crop_image($sourcePath, $desPath, $width = '320', $height = '320')
    {
        $this->image_lib->clear();
        $config['image_library'] = 'gd2';
        $config['source_image'] = $sourcePath;
        $config['new_image'] = $desPath;
        $config['quality'] = '100%';
        $config['maintain_ratio'] = FALSE;
        $config['width'] = $width;
        $config['height'] = $height;
		$config['x_axis'] = '20';
		$config['y_axis'] = '20';
        
		$this->image_lib->initialize($config);
 
        if ($this->image_lib->crop())
            return true;
        return false;
    }
}
