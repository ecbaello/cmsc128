<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Take extends StudentInfoController {
	
	public function body(){
		if($this->ion_auth->logged_in()){
			$this->load->view('survey_form');
		}else{
			$this->load->view('login');
		}
	}
	
	public function getSurveyForm(){
		echo json_encode($this->survey_maker->getSurvey(),JSON_NUMERIC_CHECK|JSON_HEX_APOS);
	}
	
	public function submit($sn=null){
		
		if($sn==null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
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
		
		$res = $this->survey_maker->submitResults($sn,$data);
		if($res != null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Answers successfully submitted');
		return;
	}
}