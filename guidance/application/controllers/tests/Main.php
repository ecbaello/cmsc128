<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends TestsController {

	public function body()
	{
		$this->load->view('tests_nav');
	}
	
	public function get($type){
		switch($type){
			case 'tests':
				$this->getTests();
				break;
			default:
				show_404();
				return;
		}
	}
	
	public function post($type){
		switch($type){
			case 'add':
				$this->add();
				break;
			default:
				show_404();
				return;
		}
	}
	
	private function add(){
		$input = $this->input->post('data');
		
		if($input == null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		//log_message('debug',$input);die();
		$data= json_decode($input,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if(!isset($data['Title'])){
			$this->responseJSON(false,'Missing Title');
			return;
		}
		
		if(!isset($data['Description'])){
			$data['Description'] = '';
		}
		
		$result = $this->test_maker->addTest($data['Title'],$data['Description']);
		if($result!=null){
			$this->responseJSON(false,$result);
			return;
		}else{
			$this->responseJSON(true,'Added Test Successfully.');
			return;
		}
		
	}
	
	public function getTests(){
		$tests = $this->test_maker->getTests();
		$output = array();
		foreach($tests as $test){
			array_push($output,array(
				'ID'=>$test[Test_Maker::TestsPKName],
				'Title'=>$test[Test_Maker::TestsTitleFieldName],
				'Desc'=>$test[Test_Maker::TestsDescFieldName]
			));
		}
		
		echo json_encode($output,JSON_NUMERIC_CHECK);
	}
	
}