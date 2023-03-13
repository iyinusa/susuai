<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		redirect(base_url('admin/user'), 'refresh');
	}
	
	/////////////// ********** MANAGE COUNTRY ************ ///////////////////
	public function country($param1 = '', $param2 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		
		if($param1 == 'a'){
			if($param2 != '') {
				$getcountry = $this->Crud->read_single('id', $param2, 'ka_country');
				if(!empty($getcountry)){
					foreach($getcountry as $country){
						$data['e_id'] = $country->id;
						$data['e_name'] = $country->name;
						$data['e_code'] = $country->code;
						$data['e_currency'] = $country->currency;
					}
				}
			}
			
			if($_POST){
				$country_id = $_POST['country_id'];
				$name = $_POST['name'];
				$code = $_POST['code'];
				$currency = $_POST['currency'];
				
				if($country_id != ''){
					$upd_data = array(
						'name' => $name, 
						'code' => $code,
						'currency' => $currency
					);
					$upd_id = $this->Crud->update('id', $country_id, 'ka_country', $upd_data);
					if($upd_id){
						$data['err_msg'] = $this->Crud->msg('success', 'Record Updated');	
					} else {
						$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
					}
				} else {
					if($this->Crud->check('name', $name, 'ka_country') > 0){
						$data['err_msg'] = $this->Crud->msg('danger', 'Record already created');
					} else {
						$ins_data = array(
							'name' => $name, 
							'code' => $code,
							'currency' => $currency
						);
						$ins_id = $this->Crud->create('ka_country', $ins_data);
						if($ins_id){
							$data['err_msg'] = $this->Crud->msg('success', 'Record Created');	
						} else {
							$data['err_msg'] = $this->Crud->msg('warning', 'Please try later');
						}
					}
				}
			}
		} else if($param1 == 'd'){
			if($param2 != '') {
				$getcountry = $this->Crud->read_single('id', $param2, 'ka_country');
				if(!empty($getcountry)){
					foreach($getcountry as $country){
						$data['d_id'] = $country->id;
						$data['d_name'] = $country->name;
					}
				}
			}
			
			if($_POST){
				$d_country_id = $_POST['d_country_id'];
				if(isset($_POST['btnYes'])){
					$this->Crud->delete('id', $d_country_id, 'ka_country');
				}
				redirect(base_url('admin/country'), 'refresh');
			}
		}
		
		$data['allcountry'] = $this->Crud->read_order('ka_country', 'name', 'ASC');
		
		// for datatable
		$data['table_rec'] = 'admin/country_list'; // ajax table
		$data['order_sort'] = '1, "asc"'; // default ordering (0, 'asc')
		$data['no_sort'] = '3'; // sort disable columns (1,3,5)
		
		$data['title'] = 'Countries | '.app_name;
		$data['page_active'] = 'ad_country';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/country', $data);
		$this->load->view('designs/footer', $data);
	}
	// list record into datatable ajax
	public function country_list() {
		// DataTable parameters
		$table = 'ka_country';
		$column_order = array('code', 'name', 'currency', null);
		$column_search = array('code', 'name', 'currency');
		$order = array('id' => 'desc');
		$where = '';
		
		// load data into table
		$list = $this->Crud->datatable_load($table, $column_order, $column_search, $order, $where);
		$data = array();
		// $no = $_POST['start'];
		foreach ($list as $item) {
			$id = $item->id;
			$name = $item->name;
			$code = $item->code;
			$currency = $item->currency;
			
			$all_btn = '
				<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/country/a/'.$id).'"><i class="mdi mdi-pencil"></i></a>
				<a class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Delete Record" href="'.base_url('admin/country/d/'.$id).'"><i class="mdi mdi-close"></i></a>
			';
			
			$row = array();
			$row[] = $code;
			$row[] = $name;
			$row[] = $currency;
			$row[] = $all_btn;

			$data[] = $row;
		}

		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Crud->datatable_count($table, $where),
			"recordsFiltered" => $this->Crud->datatable_filtered($table, $column_order, $column_search, $order, $where),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}
	
	/////////////// ********** MANAGE STATE ************ ///////////////////
	public function state($param1 = '', $param2 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		
		if($param1 == 'a'){
			if($param2 != '') {
				$getstate = $this->Crud->read_single('id', $param2, 'ka_state');
				if(!empty($getstate)){
					foreach($getstate as $state){
						$data['e_id'] = $state->id;
						$data['e_country_id'] = $state->country_id;
						$data['e_name'] = $state->name;
					}
				}
			}
			
			if($_POST){
				$state_id = $_POST['state_id'];
				$country_id = $_POST['country_id'];
				$name = $_POST['name'];
				
				if($state_id != ''){
					$upd_data = array(
						'country_id' => $country_id, 
						'name' => $name
					);
					$upd_id = $this->Crud->update('id', $state_id, 'ka_state', $upd_data);
					if($upd_id){
						$data['err_msg'] = $this->Crud->msg('success', 'Record Updated');	
					} else {
						$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
					}
				} else {
					if($this->Crud->check2('name', $name, 'country_id', $country_id, 'ka_state') > 0){
						$data['err_msg'] = $this->Crud->msg('danger', 'Record already created');
					} else {
						$ins_data = array(
							'country_id' => $country_id, 
							'name' => $name
						);
						$ins_id = $this->Crud->create('ka_state', $ins_data);
						if($ins_id){
							$data['err_msg'] = $this->Crud->msg('success', 'Record Created');	
						} else {
							$data['err_msg'] = $this->Crud->msg('warning', 'Please try later');
						}
					}
				}
			}
		} else if($param1 == 'd'){
			if($param2 != '') {
				$getstate = $this->Crud->read_single('id', $param2, 'ka_state');
				if(!empty($getstate)){
					foreach($getstate as $state){
						$data['d_id'] = $state->id;
						$data['d_name'] = $state->name;
					}
				}
			}
			
			if($_POST){
				$d_state_id = $_POST['d_state_id'];
				if(isset($_POST['btnYes'])){
					$this->Crud->delete('id', $d_state_id, 'ka_state');
				}
				redirect(base_url('admin/state'), 'refresh');
			}
		}
		
		$data['allcountry'] = $this->Crud->read_order('ka_country', 'name', 'ASC');
		$data['allstate'] = $this->Crud->read_order('ka_state', 'name', 'ASC');
		
		// for datatable
		$data['table_rec'] = 'admin/state_list'; // ajax table
		$data['order_sort'] = '1, "asc"'; // default ordering (0, 'asc')
		$data['no_sort'] = '2'; // sort disable columns (1,3,5)
		
		$data['title'] = 'States | '.app_name;
		$data['page_active'] = 'ad_state';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/state', $data);
		$this->load->view('designs/footer', $data);
	}
	// list record into datatable ajax
	public function state_list() {
		// DataTable parameters
		$table = 'ka_state';
		$column_order = array(null, 'name', null);
		$column_search = array('name');
		$order = array('id' => 'desc');
		$where = '';
		
		// load data into table
		$list = $this->Crud->datatable_load($table, $column_order, $column_search, $order, $where);
		$data = array();
		// $no = $_POST['start'];
		foreach ($list as $item) {
			$id = $item->id;
			$country_id = $item->country_id;
			$name = $item->name;
			
			$country_name = $this->Crud->read_field('id', $country_id, 'ka_country', 'name');
			
			$all_btn = '
				<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/state/a/'.$id).'"><i class="mdi mdi-pencil"></i></a>
				<a class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Delete Record" href="'.base_url('admin/state/d/'.$id).'"><i class="mdi mdi-close"></i></a>
			';
			
			$row = array();
			$row[] = $country_name;
			$row[] = $name;
			$row[] = $all_btn;

			$data[] = $row;
		}

		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Crud->datatable_count($table, $where),
			"recordsFiltered" => $this->Crud->datatable_filtered($table, $column_order, $column_search, $order, $where),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}
	
	/////////////// ********** MANAGE USERS ************ ///////////////////
	public function user($param1 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['edit'] = FALSE;
		
		// edit user role here
		if($param1 != ''){
			if($_POST){
				$role = $_POST['role'];
				$activate = $_POST['activate'];
				$upd_data = array('role'=>$role, 'activate'=>$activate);
				$this->Crud->update('id', $param1, 'ka_user', $upd_data);
				redirect(base_url('admin/user'), 'refresh');
			} else {
				$getuser = $this->Crud->read_single('id', $param1, 'ka_user');
				if(!empty($getuser)){
					foreach($getuser as $user){
						$data['e_id'] = $user->id;
						$data['e_name'] = ucwords($user->othername).' '.ucwords($user->lastname);
						$data['e_role'] = $user->role;
						$data['e_activate'] = $user->activate;
						$data['edit'] = TRUE;	
					}
				}
			}
		}
		
		$data['alluser'] = $this->Crud->read_order('ka_user', 'othername', 'ASC');
		
		// for datatable
		$data['table_rec'] = 'admin/user_list'; // ajax table
		$data['order_sort'] = '2, "asc"'; // default ordering (0, 'asc')
		$data['no_sort'] = '1,7'; // sort disable columns (1,3,5)
		
		$data['title'] = 'User Accounts | '.app_name;
		$data['page_active'] = 'ad_user';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/user', $data);
		$this->load->view('designs/footer', $data);
	}
	// list record into datatable ajax
	public function user_list() {
		// DataTable parameters
		$table = 'ka_user';
		$column_order = array('reg_date', null, 'othername', 'email', 'phone', 'sex', 'role', null);
		$column_search = array('reg_date', 'othername', 'email', 'phone', 'sex', 'role');
		$order = array('id' => 'desc');
		$where = '';
		
		// load data into table
		$list = $this->Crud->datatable_load($table, $column_order, $column_search, $order, $where);
		$data = array();
		// $no = $_POST['start'];
		foreach ($list as $item) {
			$id = $item->id;
			$othername = $item->othername;
			$lastname = $item->lastname;
			$email = $item->email;
			$phone = $item->phone;
			$sex = $item->sex;
			$status = $item->status;
			$role = $item->role;
			$pics = $item->pics;
			$activate = $item->activate;
			$reg_date = $item->reg_date;
			
			//get logo
			$logo_path = 'assets/images/users/avatar300.png';
			$getimg = $this->Crud->read_single('id', $pics, 'ka_img');
			if(!empty($getimg)){
				foreach($getimg as $img){
					$logo_path = $img->pics_square;	
				}
			}
			
			if($activate == 0){$alert = 'danger';} else {$alert = '';}
			if($role == 'Admin'){$alert = 'success';}
			
			$all_btn = '
				<a class="btn btn-xs btn-primary" type="button" data-toggle="tooltip" title="Manage Record" href="'.base_url('admin/user/'.$id).'"><i class="mdi mdi-pencil"></i></a>
				<a class="btn btn-xs btn-success" type="button" data-toggle="tooltip" title="View Record" href="'.base_url('profile/v/'.$id).'"><i class="mdi mdi-eye"></i></a>
			';
			
			$row = array();
			$row[] = date('M d, Y', strtotime($reg_date));
			$row[] = '<img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30">';
			$row[] = '<a href="'.base_url('profile/v/'.$id).'">'.ucwords($othername).' '.ucwords($lastname).'</a>';
			$row[] = $email;
			$row[] = $phone;
			$row[] = $sex;
			$row[] = $role;
			$row[] = $all_btn;

			$data[] = $row;
		}

		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Crud->datatable_count($table, $where),
			"recordsFiltered" => $this->Crud->datatable_filtered($table, $column_order, $column_search, $order, $where),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}
	
	/////////////// ********** MANAGE PERSONAL SAVINGS ************ ///////////////////
	public function personal($param1 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['allpersonal'] = $this->Crud->read('ka_personal');
		
		// for datatable
		$data['table_rec'] = 'admin/personal_list'; // ajax table
		$data['order_sort'] = '0, "desc"'; // default ordering (0, 'asc')
		$data['no_sort'] = '2,3'; // sort disable columns (1,3,5)
		
		$data['title'] = 'Personal Savings | '.app_name;
		$data['page_active'] = 'ad_personal';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/personal', $data);
		$this->load->view('designs/footer', $data);
	}
	// list record into datatable ajax
	public function personal_list() {
		// DataTable parameters
		$table = 'ka_personal';
		$column_order = array('reg_date', null, null, 'name', 'target', 'type', null);
		$column_search = array('reg_date', 'name', 'target', 'type');
		$order = array('id' => 'desc');
		$where = '';
		
		// load data into table
		$list = $this->Crud->datatable_load($table, $column_order, $column_search, $order, $where);
		$data = array();
		// $no = $_POST['start'];
		foreach ($list as $item) {
			$id = $item->id;
			$user_id = $item->user_id;
			$name = $item->name;
			$target = $item->target;
			$type = $item->type;
			$duration = $item->duration;
			$cycle = $item->cycle;
			$expired = $item->expired;
			$reg_date = $item->reg_date;
			
			// get user detail
			$p_saving_by = $this->Crud->read_field('id', $user_id, 'ka_user', 'othername').' '.$this->Crud->read_field('id', $user_id, 'ka_user', 'lastname');
			$user_img_id = $this->Crud->read_field('id', $user_id, 'ka_user', 'pics');
			$logo_path = $this->Crud->read_field('id', $user_img_id, 'ka_img', 'pics_square');
			if($logo_path == ''){$logo_path = 'assets/images/users/avatar300.png';}
			
			if($expired == 1){$alert = 'danger';} else {$alert = '';}
			
			$row = array();
			$row[] = $id;
			$row[] = date('M d, Y', strtotime($reg_date));
			$row[] = '<img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30">';
			$row[] = '<a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a>';
			$row[] = '<span class="text-'.$alert.'">'.ucwords($name).'</span>';
			$row[] = '<span class="text-'.$alert.'">'.number_format((float)$target,2).'</span>';
			$row[] = $type;
			$row[] = $cycle.' of '.$duration;

			$data[] = $row;
		}

		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Crud->datatable_count($table, $where),
			"recordsFiltered" => $this->Crud->datatable_filtered($table, $column_order, $column_search, $order, $where),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}
	
	/////////////// ********** MANAGE CONTRIBUTIONS ************ ///////////////////
	public function contribution($param1 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['allcont'] = $this->Crud->read('ka_contribute');
		
		// for datatable
		$data['table_rec'] = 'admin/contribution_list'; // ajax table
		$data['order_sort'] = '0, "desc"'; // default ordering (0, 'asc')
		$data['no_sort'] = '2,3,4'; // sort disable columns (1,3,5)
		
		$data['title'] = 'Contributions | '.app_name;
		$data['page_active'] = 'ad_contribute';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/contribution', $data);
		$this->load->view('designs/footer', $data);
	}
	// list record into datatable ajax
	public function contribution_list() {
		// DataTable parameters
		$table = 'ka_contribute';
		$column_order = array('id', 'reg_date', null, null, null, 'amt', 'type');
		$column_search = array('reg_date', 'amt', 'type');
		$order = array('id' => 'desc');
		$where = '';
		
		// load data into table
		$list = $this->Crud->datatable_load($table, $column_order, $column_search, $order, $where);
		$data = array();
		// $no = $_POST['start'];
		foreach ($list as $item) {
			$id = $item->id;
			$saving_id = $item->saving_id;
			$amt = $item->amt;
			$type = $item->type;
			$reg_date = $item->reg_date;
			
			// get savings detail
			$save_name = $this->Crud->read_field('id', $saving_id, 'ka_personal', 'name');
			$user_id = $this->Crud->read_field('id', $saving_id, 'ka_personal', 'user_id');
			$p_saving_by = $this->Crud->read_field('id', $user_id, 'ka_user', 'othername').' '.$this->Crud->read_field('id', $user_id, 'ka_user', 'lastname');
			$user_img_id = $this->Crud->read_field('id', $user_id, 'ka_user', 'pics');
			$logo_path = $this->Crud->read_field('id', $user_img_id, 'ka_img', 'pics_square');
			if($logo_path == ''){$logo_path = 'assets/images/users/avatar300.png';}
			
			$row = array();
			$row[] = $id;
			$row[] = date('M d, Y h:i:s A', strtotime($reg_date));
			$row[] = '<img src="'.base_url($logo_path).'" alt="user" class="img-circle" width="30">';
			$row[] = '<a href="'.base_url('profile/v/'.$user_id).'">'.$p_saving_by.'</a>';
			$row[] = ucwords($save_name);
			$row[] = number_format((float)$amt,2);
			$row[] = $type;

			$data[] = $row;
		}

		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Crud->datatable_count($table, $where),
			"recordsFiltered" => $this->Crud->datatable_filtered($table, $column_order, $column_search, $order, $where),
			"data" => $data,
		);
		
		//output to json format
		echo json_encode($output);
	}
	
	/////////////// ********** MANAGE VAULT ************ ///////////////////
	public function vault($param1 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['allvault'] = $this->Crud->read('ka_vault');
		
		$data['title'] = 'Vaults | '.app_name;
		$data['page_active'] = 'ad_vault';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/vault', $data);
		$this->load->view('designs/footer', $data);
	}
	
	/////////////// ********** MANAGE BANK ************ ///////////////////
	public function bank($param1 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		// perform api bank handshake
		$record = 'API and Native Banks already updated';
		$gettoken = json_decode($this->Crud->pay_token());
		if($gettoken) {
			if($gettoken->status == 'success'){
				// get all countries
				$getcountry = $this->Crud->read('ka_country');
				if(!empty($getcountry)) {
					foreach($getcountry as $country){
						$country_id = $country->id;	
						$country_code = $country->code;
						
						if($country_code == 'GHS'){$country_code = 'GH';}
						if($country_code == 'KES'){$country_code = 'KE';}
						
						$api_banks = json_decode($this->Crud->pay_getbank($country_code, $gettoken->token));
						if($api_banks->message == 'banks Fetched'){
							$api_banks_data = $api_banks->data;
							foreach($api_banks_data as $key => $value){
								if($this->Crud->check3('country_id', $country_id, 'code', $key, 'name', $value, 'ka_bank') <= 0){ // check if not updated
									if($this->Crud->check('code', $key, 'ka_bank') > 0){
										// update record 
										$upd_data = array('country_id' => $country_id, 'name' => $value);
										$this->Crud->update('code', $key, 'ka_bank', $upd_data);
										$record = 'Update API Banks update now handshaked with Native Banks';
									} else {
										// register record
										$ins_data = array('country_id' => $country_id, 'code' => $key, 'name' => $value);
										$this->Crud->create('ka_bank', $ins_data);
										$record = 'New API Banks update now handshaked with Native Banks';
									}
								}
							}
						}	
					}
				}
			}
		}
		
		$data['err_msg'] = $this->Crud->msg('info', $record);
		$data['allbank'] = $this->Crud->read_order('ka_bank', 'name', 'ASC');
		
		$data['title'] = 'Banks | '.app_name;
		$data['page_active'] = 'ad_bank';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/bank', $data);
		$this->load->view('designs/footer', $data);
	}
	
	/////////////// ********** MANAGE TRANSACTIONS ************ ///////////////////
	public function transaction() {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		// get moneywave wallet balance
		$wallet_msg = '';
		$wallet_balance = 0;
		$gettoken = json_decode($this->Crud->pay_token());
		if($gettoken) {
			if($gettoken->status == 'success'){
				$wallet = json_decode($this->Crud->pay_wallet_balance($gettoken->token), true);
				if($wallet['status'] == 'success'){
					$wallet_data = $wallet['data'];
					foreach($wallet_data as $wdata){
						if($wdata['name'] == 'Wallet') {
							$wallet_balance = $wdata['balance'];
						}
					}
					$wallet_msg = 'Handshaked with Moneywave';
				} else {
					$wallet_msg = 'Could not talk to Moneywave';
				}
			}
		}
		
		$data['err_msg'] = $this->Crud->msg('info', $wallet_msg);
		$data['wallet_balance'] = $wallet_balance;
		$data['alltrans'] = $this->Crud->read('ka_transaction');
		
		$data['title'] = 'Transactions | '.app_name;
		$data['page_active'] = 'ad_transaction';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/transaction', $data);
		$this->load->view('designs/footer', $data);
	}
	
	/////////////// ********** MANAGE OFFERS ************ ///////////////////
	public function offer($param1 = '', $param2 = '') {
		//register redirect page
		$s_data = array ('kas_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');	
		} else {
			$kas_user_role = $this->session->userdata('kas_user_role');
			$user_id = $this->session->userdata('kas_id');
			$permit = array('Admin');
			if(!in_array($kas_user_role, $permit)){
				redirect(base_url('dashboard'), 'refresh');	
			}
		}
		
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		
		if($param1 == 'a_partner'){
			if($param2 != '') {
				$getpartner = $this->Crud->read_single('id', $param2, 'ka_offer_partner');
				if(!empty($getpartner)){
					foreach($getpartner as $partner){
						$data['e_id'] = $partner->id;
						$data['e_country_id'] = $partner->country_id;
						$data['e_name'] = $partner->name;
					}
				}
			}
			
			if($_POST){
				$partner_id = $_POST['partner_id'];
				$country_id = $_POST['country_id'];
				$name = $_POST['name'];
				
				if($partner_id != ''){
					$upd_data = array(
						'country_id' => $country_id, 
						'name' => $name
					);
					$upd_id = $this->Crud->update('id', $partner_id, 'ka_offer_partner', $upd_data);
					if($upd_id){
						$data['err_msg'] = $this->Crud->msg('success', 'Record Updated');	
					} else {
						$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
					}
				} else {
					if($this->Crud->check2('country_id', $country_id, 'name', $name, 'ka_offer_partner') > 0){
						$data['err_msg'] = $this->Crud->msg('danger', 'Record already created');
					} else {
						$ins_data = array(
							'country_id' => $country_id,
							'name' => $name
						);
						$ins_id = $this->Crud->create('ka_offer_partner', $ins_data);
						if($ins_id){
							$data['err_msg'] = $this->Crud->msg('success', 'Record Created');	
						} else {
							$data['err_msg'] = $this->Crud->msg('warning', 'Please try later');
						}
					}
				}
			}
		} else if($param1 == 'd_partner'){
			if($param2 != '') {
				$getpartner = $this->Crud->read_single('id', $param2, 'ka_offer_partner');
				if(!empty($getpartner)){
					foreach($getpartner as $partner){
						$data['d_id'] = $partner->id;
						$data['d_name'] = $partner->name;
					}
				}
			}
			
			if($_POST){
				$d_partner_id = $_POST['d_partner_id'];
				if(isset($_POST['btnYes'])){
					$this->Crud->delete('id', $d_partner_id, 'ka_offer_partner');
				}
				redirect(base_url('admin/offer'), 'refresh');
			}
		} else if($param1 == 'a_com'){
			if($param2 != '') {
				$getcom = $this->Crud->read_single('id', $param2, 'ka_offer_commission');
				if(!empty($getcom)){
					foreach($getcom as $com){
						$data['e_id'] = $com->id;
						$data['e_partner_id'] = $com->partner_id;
						$data['e_name'] = $com->name;
						$data['e_com'] = $com->com;
					}
				}
			}
			
			if($_POST){
				$com_id = $_POST['com_id'];
				$partner_id = $_POST['partner_id'];
				$name = $_POST['name'];
				$com = $_POST['com'];
				
				if($com_id != ''){
					$upd_data = array(
						'partner_id' => $partner_id, 
						'name' => $name,
						'com' => $com
					);
					$upd_id = $this->Crud->update('id', $com_id, 'ka_offer_commission', $upd_data);
					if($upd_id){
						$data['err_msg'] = $this->Crud->msg('success', 'Record Updated');	
					} else {
						$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
					}
				} else {
					if($this->Crud->check2('partner_id', $partner_id, 'name', $name, 'ka_offer_commission') > 0){
						$data['err_msg'] = $this->Crud->msg('danger', 'Record already created');
					} else {
						$ins_data = array(
							'partner_id' => $partner_id, 
							'name' => $name,
							'com' => $com
						);
						$ins_id = $this->Crud->create('ka_offer_commission', $ins_data);
						if($ins_id){
							$data['err_msg'] = $this->Crud->msg('success', 'Record Created');	
						} else {
							$data['err_msg'] = $this->Crud->msg('warning', 'Please try later');
						}
					}
				}
			}
		} else if($param1 == 'd_com'){ 
			if($param2 != '') {
				$getcom = $this->Crud->read_single('id', $param2, 'ka_offer_commission');
				if(!empty($getcom)){
					foreach($getcom as $com){
						$data['d_id'] = $com->id;
						$data['d_name'] = $com->name;
					}
				}
			}
			
			if($_POST){
				$d_com_id = $_POST['d_com_id'];
				if(isset($_POST['btnYes'])){
					$this->Crud->delete('id', $d_offer_id, 'ka_offer_commission');
				}
				redirect(base_url('admin/offer'), 'refresh');
			}
		} else if($param1 == 'm_offer'){
			if($param2 != '') {
				$getoffer = $this->Crud->read_single('id', $param2, 'ka_offer');
				if(!empty($getoffer)){
					foreach($getoffer as $offer){
						$data['e_id'] = $offer->id;
						$data['e_offer_user_id'] = $offer->user_id;
						$data['e_offer_no'] = $offer->offer_no;
						$data['e_product_link'] = $offer->product_link;
						$data['e_offer_link'] = $offer->offer_link;
						$data['e_offer_interest'] = $offer->interest;
						$data['e_status'] = $offer->status;
						$data['e_reason'] = $offer->reasons;
					}
				}
			}
			
			if($_POST){
				$offer_id = $_POST['offer_id'];
				$status = $_POST['status'];
				$reason = $_POST['reason'];
				$offer_user_id = $_POST['offer_user_id'];
				$offer_no = $_POST['offer_no'];
				$offer_interest = $_POST['offer_interest'];
				
				// get user info
				$user_othername = '';
				$user_email = '';
				$user_phone = '';
				$user_curr = '';
				$getuser = $this->Crud->read_single('id', $offer_user_id, 'ka_user');
				if(!empty($getuser)){
					foreach($getuser as $user) {
						$user_othername = $user->othername;
						$user_email = $user->email;
						$user_phone = $user->phone;
						$user_curr = $this->Crud->country_data($user->country, 'currency');
					}
				}
				
				if($offer_id != '' && $status!=''){
					$upd_data = array(
						'interest' => $offer_interest,
						'status' => $status, 
						'reasons' => $reason
					);
					$upd_id = $this->Crud->update('id', $offer_id, 'ka_offer', $upd_data);
					if($upd_id){
						if($status == 'Declined') {
							$resp_text = 'Offer is Declined';
							
							$n_title = 'Sorry! Offer Money Back Declined';
							$n_details = 'Regret to inform you that  '.$user_curr.number_format((float)$offer_interest, 2).' Money Payback to your Offer ('.$offer_no.') was declined with this Reason ('.$reason.'). Kindly note this fault and try on another offer by following our Terms and Conditions';
						} else if($status == 'Approved') {
							$resp_text = 'Offer is Approved';
							
							// credit user Voluntary Vault
							$purpose = 'Money back from '.$offer_no.' Offer';
							$vv_data = array (
								'user_id' => $offer_user_id,
								'amount' => $offer_interest,
								'type' => 'Offer',
								'purpose' => $purpose,
								'action' => 'Save',
								'trans_msg' => 'Offer Completed',
								'trans_status' => 'Completed',
								'reg_date' => date(fdate)
							);
							$this->Crud->create('ka_voluntary', $vv_data);
							$n_title = 'Congratulation! Offer Money Back';
							$n_details = 'Your Volutary Vault/Wallet has been credited with '.$user_curr.number_format((float)$offer_interest, 2).' Money Payback to your Offer ('.$offer_no.')';
						} else {
							$resp_text = 'Record Updated';
						}
						
						$data['err_msg'] = $this->Crud->msg('success', $resp_text);	
						
						// prepare push notification
						$n_user_id = $offer_user_id; 
						$n_othername = $user_othername; 
						$n_email = $user_email; 
						$n_phone = $user_phone;
						$n_item_id = $offer_id;
						$n_hash = md5(time());
						$n_item = 'offer';
						
						$this->Crud->notify($n_user_id, $n_othername, $n_email, $n_phone, $n_item_id, $n_item, $n_title, $n_details, 'email', $n_hash);
					} else {
						$data['err_msg'] = $this->Crud->msg('info', 'No Record Changes');
					}
				}
			}
		}
		
		$data['allcountry'] = $this->Crud->read_order('ka_country', 'name', 'ASC');
		$data['allpartner'] = $this->Crud->read_order('ka_offer_partner', 'name', 'ASC');
		$data['allcom'] = $this->Crud->read_order('ka_offer_commission', 'name', 'ASC');
		$data['alloffer'] = $this->Crud->read_order('ka_offer', 'id', 'DESC');
		
		$data['title'] = 'Offers | '.app_name;
		$data['page_active'] = 'ad_offer';
		
		$this->load->view('designs/header', $data);
		$this->load->view('admin/offer', $data);
		$this->load->view('designs/footer', $data);
	}
}
