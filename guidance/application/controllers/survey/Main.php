<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends BaseController {

	public function body()
	{
		if($this->ion_auth->logged_in()){
			if($this->ion_auth->is_admin()){
				$this->load->view('survey_nav');
			}else{
				$this->load->view('survey_form',array('answered'=>$this->survey_maker->hasAnswered($this->ion_auth->user()->row()->username)));
			}
		}else{
			$this->load->view('login');
		}
	}
	
	public function changeInterpretation(){
		$input = $this->input->post('data');
		
		if($input == null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		$data= json_decode($input,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if(!isset($data['SID'])||!isset($data['ID'])||!isset($data['Interpretation'])){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		
		$this->survey_maker->setInterpretation($data['SID'],$data['ID'],$data['Interpretation']);
		$this->responseJSON(true,'Success');
		return;
	}
}