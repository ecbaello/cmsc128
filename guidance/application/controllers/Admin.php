<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends BaseController {
	
	public function body()
	{
		if(!$this->ion_auth->logged_in()){
			$this->load->view('login');
		}else{
			if($this->ion_auth->is_admin()){
				$this->load->view('admin');
			}else{
				$this->load->view('permission_error');
			}
		}
	}
	
	public function action($mode=null){
		$this->permissionRestrict();
		if($mode===null){
			$this->responseJSON(false,'Incomplete arguments.');
			return;
		}
		$data = $this->input->post('data');
		$data= json_decode($data,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		};
		
		if($data==null){
			$this->responseJSON(false,'Empty input');
			return;
		}
		
		switch($mode){
			case 'changeuser':
				if(!isset($data['newUsername'])||!isset($data['userPassword'])){
					$this->responseJSON(false,'Missing input.');
					return;
				}
				if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id,$data['userPassword'])){
					$this->responseJSON(false,'Wrong password.');
					return;
				}
				
				$res = $this->ion_auth->update(1,array(
					'username'=>$data['newUsername']
				));
				
				if(!$res){
					$this->responseJSON(false,'Unsuccessful. Please choose a different username.');
					return;
				}
				
				$this->responseJSON(true,'Username changed.');
				return;
			case 'changepass':
				if(!isset($data['newPassword1'])||!isset($data['newPassword2'])||!isset($data['passPassword'])){
					$this->responseJSON(false,'Missing input');
					return;
				}
				if($data['newPassword1']!==$data['newPassword2']){
					$this->responseJSON(false,'Passwords don\'t match');
					return;
				}
				if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id,$data['passPassword'])){
					$this->responseJSON(false,'Wrong password.');
					return;
				}
				$res = $this->ion_auth->update(1,array(
					'password'=>$data['newPassword1']
				));
				
				if(!$res){
					$this->responseJSON(false,'Unsuccessful. Please choose a different password.');
					return;
				}
				
				$this->responseJSON(true,'Password changed.');
				return;
			case 'changeemail':
				if(!isset($data['newEmail'])||!isset($data['emailPassword'])){
					$this->responseJSON(false,'Missing input.');
					return;
				}
				if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id,$data['emailPassword'])){
					$this->responseJSON(false,'Wrong password.');
					return;
				}
				$res = $this->ion_auth->update(1,array(
					'email'=>$data['newEmail']
				));
				
				if(!$res){
					$this->responseJSON(false,'Unsuccessful. Please choose a different email.');
					return;
				}
				
				$this->responseJSON(true,'Email changed.');
				return;
			default:
				show_404();
		}
	}
	
	public function getAccount(){
		$this->permissionRestrict();
		$output = array();
		$output['username'] = $this->ion_auth->user()->row()->username;
		$output['email']= $this->ion_auth->user()->row()->email;
		echo json_encode($output);
	}

	public function db(){
		$this->permissionRestrict();
		$this->load->view('header');
		$this->load->view('database_init');
		$this->load->view('footer');
	}
	
	public function initDB(){
		$this->permissionRestrict();
		$data = $this->input->post('data');
		if($data==null){
			$this->responseJSON(false,'Missing password.');
			return;
		}
		if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id,$data)){
			$this->responseJSON(false,'Wrong password.');
			return;
		}
		$this->load->model('student_information');
		$this->student_information->initDefaults();
		$this->responseJSON(true,'Initialized');
		return;
	}
	
}