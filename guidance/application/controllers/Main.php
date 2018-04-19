<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends BaseController {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 
	public function body()
	{
		$this->load->view("welcome");
	}
	
	public function login(){
		
		$data=$this->input->post('data');
		if($data == null){
			$this->responseJSON(false,'Input is empty');
			return;
		}
		$data = json_decode($data,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}

		if(!isset($data['username'])||!isset($data['password'])){
			$this->responseJSON(false,'Missing username or password.');
			return;
		}
		
		if(!$this->ion_auth->login($data['username'],$data['password'])){
			$this->responseJSON(false,'Invalid login');
			return;
		}
		
		$this->responseJSON(true,'Logged-in successfully');
		return;
		
	}
	
	public function logout(){
		$this->ion_auth->logout();
		redirect(base_url());
	}
	
}
