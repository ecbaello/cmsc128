<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends TestsController {

	public function body()
	{
		//$this->load->view('tests_edit');
		show_404();
	}
	
	public function post(){
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
		
		$validation = $this->validateInput($data);
		if($validation !== null){
			$this->responseJSON(false,$validation);
			return;
		}
		$this->test_maker->editTest($data['ID'],array(
			'Title'=>$data['Title'],
			'Desc'=>$data['Desc']
		));
		
		$this->test_maker->setQuestions($data['Title'],$data['Questions']);
		
		$this->responseJSON(true,'Test Edited Successfully');
		return;
	}
	
	public function test($testTitle=null){
		
		if($testTitle==null){
			show_404();
			return;
		}
		$testTitle = urldecode($testTitle);
		if($this->test_maker->getTestID($testTitle)===null)
			show_404();
		$this->showEditForm($testTitle);
		
	}
	
	private function showEditForm($testTitle){
		$this->load->view('header');
		$this->load->view('tests_edit',array(
			'test'=>json_encode($this->getTestData($testTitle),JSON_HEX_APOS)
		));
		$this->load->view('footer');
		
	}
	
	private function getTestData($testTitle){
		$output = array();
		$output['Questions'] = $this->test_maker->getQuestions($testTitle);
		$output['Title']=$testTitle;
		$output['ID']=$this->test_maker->getTestID($testTitle);
		$output['Desc']=$this->test_maker->getTestDescription($testTitle);
		
		//echo json_encode($output,JSON_NUMERIC_CHECK);
		//return json_encode($output,JSON_NUMERIC_CHECK);
		return $output;
	}
	
	private function validateInput($input){
		if(!isset($input['ID']) || !isset($input['Desc']) || !isset($input['Title']) || !isset($input['Questions']))
			return 'Invalid Test Input';
		
		if($this->test_maker->getTestByID($input['ID'])===null)
			return 'Invalid Test ID';
		
		$testID=$this->test_maker->getTestID($input['Title']);
		if($testID!==null && $testID!=$input['ID'] && $this->test_maker->getTestFlag($testID) !== TestsFlag::DELETED)
			return 'Test Title Must Be Unique';
		
		foreach($input['Questions'] as $key=>$question){
			if($question=='')
				return 'Invalid Questions Input at Question '.$key+1;
			
			if(!isset($question['Choices']))
				return 'Please Input At Least One Choice in Question '.$key+1;
			
			foreach($question['Choices'] as $key2=>$choice){
				if($choice == '')
					return 'Invalid choices at question '.$key+1;
				if(!isset($choice['Value']))
					return 'Invalid choices at question '.$key+1;
				if($choice['Value']=='')
					return 'Empty choices are not allowed.';
			}
		}
		
		return null;
			
	}
	
}