<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends surveyController {

	public function body()
	{
		$this->load->view('survey_form');
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
		
		$res = $this->test_maker->submitResults($sn,$data);
		if($res != null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Answers successfully submitted');
		return;
	}
	
	public function getResults($sn=null){
		print('<pre>');print_r($this->test_maker->getResults($sn));print('</pre>');die();
	}
}