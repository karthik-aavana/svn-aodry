<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        $this->load->helper('image_upload_helper');
        $this->load->library(array('ion_auth', 'form_validation'));
	}

	function index(){
		$data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		$data['branch_code'] = array('name' => 'branch_code',
				'id' => 'branch_code',
				'type' => 'text',
				'value' => $this->form_validation->set_value('branch_code'),
			);
		$data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);

		$data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

		$this->load->view("super_admin/auth/login",$data);

	}
	function login(){


			$remember = (bool)$this->input->post('remember');
			if ($this->ion_auth->super_admin_login($this->input->post('branch_code'),$this->input->post('identity'), $this->input->post('password'), $remember))
			{

				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('superadmin/auth/dashboard', 'refresh');
			}
			else
			{
				// if the login was un-successful
				// redirect them back to the login page
				$this->session->sess_destroy();
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('superadmin/auth', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
			}

	}

	function dashboard(){
 
		$this->load->view("super_admin/dashboard");

	}

		/**
	 * Log the user out
	 */
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		// $logout = $this->ion_auth->logout();
$this->session->sess_destroy();
		// redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');
	}

}
