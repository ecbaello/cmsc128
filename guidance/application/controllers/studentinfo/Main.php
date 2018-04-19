<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends StudentInfoController {
	
	//Override
	protected  function permissionRestrict(){
		
	}
	
	public function body()
	{
		if($this->ion_auth->logged_in()){
			if($this->ion_auth->is_admin()){
				$this->load->view('student_info_nav');
			}else{
				$this->permissionError();
				return;
			}
		}else{
			$this->load->view('login');
		}
	}
	
}