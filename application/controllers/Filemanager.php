<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Filemanager extends MY_Controller {
	public $branch_dir;
	public function __construct(){
		parent::__construct();
		$this->load->model('model_tool_image');
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
		$this->branch_dir = 'BRANCH-'.$branch_id;
	}

	public function index() {

		$file_manager_module_id = $this->config->item('file_manager_module');
        $data['file_manager_module_id'] = $file_manager_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($file_manager_module_id, $modules, $privilege);
        $access_common_settings = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

		$d = $this->GetDirectoryFiles();
		$data = array_merge($d,$data);
		$this->load->view('layout/header',$data);
		$this->load->view('common/filemanager',$data);
		$this->load->view('layout/footer',$data);
	}

	public function LoadManager(){
		$data = $this->GetDirectoryFiles();
		$this->load->view('common/file_view',$data);
	}

	public function GetDirectoryFiles(){
		$file_manager_module_id = $this->config->item('file_manager_module');
        $data['file_manager_module_id'] = $file_manager_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($file_manager_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
		$server = site_url();
		/*$branch_id = $this->session->userdata('SESS_BRANCH_ID');
		$branch_dir = 'BRANCH-'.$branch_id;*/
		$filter_name = $this->input->get('filter_name');
		if (isset($filter_name)) {
			/*echo $filter_name;
			exit();*/
			$filter_name = rtrim(str_replace('*', '', $filter_name), '/');
		} else {
			$filter_name = null;
		}
		$dir_image = str_replace("\\","/", DIR_IMAGE);
		// Make sure we have the correct directory
		$directory = $this->input->get('directory');
		if (isset($directory)) {

			$directory = rtrim($dir_image .$this->branch_dir .'/' . str_replace('*', '', $directory), '/');
		} else {
			$directory = $dir_image . $this->branch_dir;
			if(!is_dir($directory)){
				mkdir($directory, 0777, TRUE);
			}
		}
		$page = $this->input->get('page');
		if (isset($page)) {
			$page = $page;
		} else {
			$page = 1;
		}

		$directories = array();
		$files = array();

		$data['images'] = array();

		if (substr(str_replace('\\', '/', realpath($directory . '/')), 0, strlen($dir_image . $this->branch_dir)) == $dir_image . $this->branch_dir) {
			// Get directories

			$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

			if (!$directories) {
				$directories = array();
			}

			// Get files
			$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF,pdf,PDF,doc,DOC,docx,DOCX,xml,XML,csv,CSV,xls,XLS,xlsx,XLSX}', GLOB_BRACE);

			if (!$files) {
				$files = array();
			}
		}
		/*print_r($files);*/
		// Merge directories and files
		$images = array_merge($directories, $files);

		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);
		
		foreach ($images as $image) {
			$name = str_split(basename($image), 14);
			
			if (is_dir($image)) {
				$url = '';
				
				$target = $this->input->get('target');
				if (isset($target)) {
					$url .= '&target=' . $target;
				}
				$thumb = $this->input->get('thumb');
				if (isset($thumb)) {
					$url .= '&thumb=' . $thumb;
				}

				$data['images'][] = array(
					'thumb' => '',
					'name'  => implode(' ', $name),
					'type'  => 'directory',
					'path'  => substr($image, strlen($dir_image)),
					'href'  => site_url('filemanager/LoadManager').'?directory=' .substr($image, strlen($dir_image . $this->branch_dir.'/')) . $url,
				);
			} elseif (is_file($image)) {
				$data['images'][] = array(
					'thumb' => $this->model_tool_image->resize(substr($image, strlen($dir_image)), 100, 100),
					'name'  => implode(' ', $name),
					'type'  => mime_content_type($image),
					'path'  => substr($image, strlen($dir_image)),
					'href'  => base_url() . 'assets/images/' . substr($image, strlen($dir_image))
				);
			}
		}
		
		$data['heading_title'] = 'File Manager';
		$data['text_no_results'] = 'Sorry results not found!';
		$data['text_confirm'] = 'Are you sure you want to delete this folder/file?';
		$data['entry_search'] = 'Search';
		$data['entry_folder'] = 'Enter Folder Name';
		$data['button_parent'] = 'Go to Parent';
		$data['button_refresh'] = 'Refresh';
		$data['button_upload'] = 'Upload File';
		$data['button_folder'] = 'Create Folder';
		$data['button_delete'] = 'Delete Folder/File';
		$data['button_search'] = 'Search Folder/File Name';
		$data['token'] = '';
		$this->db->select('expense_file');
		$this->db->from('expense_bill');
		$this->db->where('expense_file !=', '');
		$query = $this->db->get();
		$file_exit = $query->result_array();
		$array = array();
		foreach ($file_exit as $key => $value) {
			$expense_file = preg_replace('!\s+!', '', trim($value['expense_file']));
			$array[$expense_file] = $expense_file;
		}
		$this->db->select('purchase_file');
		$this->db->from('purchase');
		$this->db->where('purchase_file !=', '');
		$query1 = $this->db->get();
		$file_exit1 = $query1->result_array();
		$array1 = array();
		foreach ($file_exit1 as $key => $value) {
			$purchase_file = preg_replace('!\s+!', '', trim($value['purchase_file']));
			$array1[$purchase_file] = $purchase_file;
		}
		$data['file_exit'] = array_merge($array, $array1);
		
		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$data['directory'] = urlencode($directory);
		} else {
			$data['directory'] = '';
		}

		$filter_name = $this->input->get('filter_name');
		if (isset($filter_name)) {
			$data['filter_name'] = $filter_name;
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		$target = $this->input->get('target');
		if (isset($target)) {
			$data['target'] = $target;
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$data['thumb'] = $thumb;
		} else {
			$data['thumb'] = '';
		}
		if(in_array($file_manager_module_id, $data['active_add'])){
			$data['add'] = 1;
		}else{
			$data['add'] = 0;
		}
		if(in_array($file_manager_module_id, $data['active_delete'])){
			$data['delete'] = 1;
		}else{
			$data['delete'] = 0;
		}

		// Parent
		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$pos = strrpos($directory, '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($directory, 0, $pos));
			}
		}

		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}

		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}
		$data['base_url_directorty'] = site_url('filemanager/LoadManager').'?token=token'.'&directory=';
		$data['parent'] = site_url('filemanager/LoadManager').'?token=token'. $url;

		// Refresh
		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$url .= '&directory=' . urlencode($directory);
		}

		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}

		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}

		$data['refresh'] = site_url('filemanager/LoadManager').'?token=token'.$url;

		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$url .= '&directory=' . urlencode(html_entity_decode($directory, ENT_QUOTES, 'UTF-8'));
		}

		$filter_name = $this->input->get('filter_name');
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'));
		}
		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}
		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}
		//$pagination = new Pagination();
		//$pagination->total = $image_total;
		//$pagination->page = $page;
		//$pagination->limit = 16;
		//$pagination->url = site_url('common/filemanager').'?token=token'. $url . '&page={page}';
		//$data['pagination'] = $pagination->render();
		
		$config['base_url'] = site_url('filemanager');
		$config['total_rows'] = $image_total;
		$config['per_page'] = 16;
		$config['page'] = $page;
		$config['url'] = $url;
		$data['pagination'] = $this->pagination($config);
		return $data;
	}

	public function upload() {
		$file_manager_module_id = $this->config->item('file_manager_module');
        $data['file_manager_module_id'] = $file_manager_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($file_manager_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
		$json = array();
		$dir_image = str_replace("\\","/", DIR_IMAGE);
		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$directory = rtrim($dir_image . $this->branch_dir.'/' . $directory, '/');
		} else {
			$directory = $dir_image . $this->branch_dir;
		}
		
		$config = array();
		$config['upload_path'] = $directory;
		$config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xml|csv|xls|xlsx';
		//$config['max_size']      = '0';
		$config['overwrite']     = FALSE;

		$this->load->library('upload');

		$files = $_FILES;
		$total = count($files['file']['name']);
		unset($_FILES);
		
		for($i=0; $i< $total; $i++){
			$_FILES['file']['name']= $files['file']['name'][$i];
			$_FILES['file']['type']= $files['file']['type'][$i];
			$_FILES['file']['tmp_name']= $files['file']['tmp_name'][$i];
			$_FILES['file']['error']= $files['file']['error'][$i];
			$_FILES['file']['size']= $files['file']['size'][$i];    

			$this->upload->initialize($config);
			if ( ! $this->upload->do_upload('file'))
			{
				$json['error'] = $this->upload->display_errors();
			}
		}
		if(empty($json['error'])){
			$file_name=$files['file']['name'][0];
			$log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'File Manager',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'File Uploaded-'.$file_name);
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
			$json['success'] = 'Uploaded Successfully';
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function folder() {
		$file_manager_module_id = $this->config->item('file_manager_module');
        $data['file_manager_module_id'] = $file_manager_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($file_manager_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
		//$this->load->language('common/filemanager');
		$json = array();
				
		//$json['server'] = $this->input->server('REQUEST_METHOD');
		


		// Check user has permission
		//if (!$this->user->hasPermission('modify', 'common/filemanager')) {
		//	$json['error'] = 'error_permission';
		//}

		// Make sure we have the correct directory
		$dir_image = str_replace("\\","/", DIR_IMAGE);
		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$directory = rtrim($dir_image . $this->branch_dir.'/' . $directory, '/');
		} else {
			$directory = $dir_image . $this->branch_dir;
		}
		
		if(!is_dir($directory)) mkdir($directory, 0777, TRUE);
		// Check its a directory
		if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen($dir_image . $this->branch_dir)) != $dir_image . $this->branch_dir) {
			$json['error'] = 'No directory found!';
		}
			
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// Sanitize the folder name
			$folder = basename(html_entity_decode(trim($this->input->post('folder')), ENT_QUOTES, 'UTF-8'));

			$json['folder'] = $folder;
			// Validate the filename length
			if ((strlen($folder) < 3) || (strlen($folder) > 128)) {
				$json['error'] = 'Invalid file name!';
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = "Sorry, this directory already exist!";
			}
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . $folder, 0777, TRUE);
			chmod($directory . '/' . $folder, 0777);

			@touch($directory . '/' . $folder . '/' . 'index.html');

			$json['success'] = 'Directory created';
		}

		//$this->response->addHeader('Content-Type: application/json');
		//$this->response->setOutput(json_encode($json));
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function delete() {
		$file_manager_module_id = $this->config->item('file_manager_module');
        $data['file_manager_module_id'] = $file_manager_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($file_manager_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
		//$this->load->language('common/filemanager');
		$dir_image = str_replace("\\","/", DIR_IMAGE);
		$json = array();

		// Check user has permission
		//if (!$this->user->hasPermission('modify', 'common/filemanager')) {
		//	$json['error'] = 'error_permission';
		//}

		$path = $this->input->post('path');
		if (isset($path)) {
			$paths = $path;
		} else {
			$paths = array();
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			// Check path exsists
			if ($path == $dir_image . $this->branch_dir || substr(str_replace('\\', '/', realpath($dir_image . $path)), 0, strlen($dir_image . $this->branch_dir)) != $dir_image . $this->branch_dir) {
				$json['error'] = 'Something went wrong!';

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim($dir_image . $path, '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}
			$log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'File Manager',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'File Deleted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
			$json['success'] = 'Deleted Successfully!';
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}
	
	public function pagination($data) {
		$base_url = $data['base_url'];
		$total = $data['total_rows'];
		$per_page = $data['per_page'];
		$page = $data['page'];
		$url = $data['url'];
		$pages = intval($total/$per_page); if($total%$per_page != 0){$pages++;}
		$p="";
		for($i=1; $i<= $pages;$i++){
			$cls = '';
			if($page == $i) $cls = 'active_page';
			$p .= '<a class="btn directory page '.$cls.'" href="'.$base_url.'/LoadManager?page='.$i.$url.'" >'.$i.'</a>';
		}
		return $p;
	}
}
