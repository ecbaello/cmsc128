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
	 
	private $isOneTimePassword = true; 
	
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
		$toDelete = !$this->ion_auth->is_admin();
		$username = $this->ion_auth->user()->row()->username;
		$this->ion_auth->logout();
		if($toDelete && $this->isOneTimePassword){
			$this->load->database();
			$this->db->where('username',$username);
			$this->db->delete(Ion_Auth_Init::UsersTableName);
		}
		redirect(base_url());
	}
	
	public function resetPassword(){
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

		if(!isset($data['Code'])||!isset($data['Password1'])||!isset($data['Password2'])){
			$this->responseJSON(false,'Missing code or password.');
			return;
		}
		if($data['Password1']!=$data['Password2']){
			$this->responseJSON(false,'Passwords do not match.');
			return;
		}
		$this->load->database();
		$this->db->select('forgotten_password_code');
		$this->db->where('id',1);
		$res = $this->db->get(Ion_Auth_Init::UsersTableName)->result_array()[0];
		if($data['Code'] == $res['forgotten_password_code']){
			$succ = $this->ion_auth->update(1,array(
				'password'=>$data['Password1']
			));
			$this->responseJSON($succ,$succ?'Success':'Invalid new password');
			return;
		}else{
			$this->responseJSON(false,'Code does not match.');
			return;
		}
		
	}
	
	public function sendResetPassword(){
		$resetCode = bin2hex(openssl_random_pseudo_bytes(12));
		$this->load->database();
		$this->db->where('id',1);
		$this->db->update(Ion_Auth_Init::UsersTableName,array(
			'forgotten_password_code'=>$resetCode
		));
		//email
		return;
	}
}
