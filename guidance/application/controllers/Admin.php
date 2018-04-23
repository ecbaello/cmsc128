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
				if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->id,$data['userPassword'])){
					$this->responseJSON(false,'Wrong password.');
					return;
				}
				$this->responseJSON(true,'Waw.');
					return;
				$this->changeUsername($data['newUsername']);
				break;
			case 'changepass':
				if(!isset($data['newPassword1'])||!isset($data['newPassword2'])||!isset($data['passPassword'])){
					$this->responseJSON(false,'Missing input');
					return;
				}
				$this->changePassword($data['newPassword1'],$data['newPassword2']);
				break;
			case 'changeemail':
				if(!isset($data['newEmail'])||!isset($data['emailPassword'])){
					$this->responseJSON(false,'Missing input.');
					return;
				}
				$this->changeEmail($data['newEmail']);
				break;
		}
	}
	
	public function getAccount(){
		$this->permissionRestrict();
		$output = array();
		$output['username'] = $this->ion_auth->user()->row()->username;
		$output['email']= $this->ion_auth->user()->row()->email;
		echo json_encode($output);
	}

	
}