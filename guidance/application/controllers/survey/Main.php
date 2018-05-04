<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends surveyController {

	public function body()
	{
		$this->load->view('survey_form');
	}
	
	public function submit($sn){
		
		
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
		
		$this->test_maker->submitResults($sn,$input);
	}
}